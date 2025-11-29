<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FlussonicService
{
    protected string $baseUrl;
    protected string $secretKey;
    protected int $tokenLifetime;
    protected int $desync;
    protected array $protocols;

    public function __construct()
    {
        $config = config('services.flussonic');

        $this->baseUrl = $config['base_url'];
        $this->secretKey = $config['secret_key'];
        $this->tokenLifetime = $config['token_lifetime'];
        $this->desync = $config['desync'];
        $this->protocols = $config['protocols'];
    }

    /**
     * Generate secure stream URL with token
     *
     * @param string $streamName Stream name only (e.g., 'bein')
     * @param int|null $userId Optional user ID for session limiting
     * @param string|null $ipAddress Optional IP address (use 'no_check_ip' to ignore)
     * @param string $protocol Protocol: 'hls', 'dash', 'rtmp'
     * @return array ['url' => string, 'expires_at' => timestamp, 'token' => string]
     * @throws \Exception
     */
    public function generateStreamUrl(
        string $streamName,
        ?int $userId = null,
        ?string $ipAddress = null,
        string $protocol = 'hls'
    ): array {
        try {
            // Get real client IP address - REQUIRED for token to work!
            // Check multiple sources to get the actual client IP (handles proxies, load balancers, etc.)
            if ($ipAddress) {
                $ip = $ipAddress;
            } else {
                // Try multiple methods to detect real client IP
                $ip = null;
                // Method 1: Check proxy headers (most common)
                if (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
                    // Cloudflare
                    $ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
                } elseif (!empty($_SERVER['HTTP_X_REAL_IP'])) {
                    // Nginx proxy
                    $ip = $_SERVER['HTTP_X_REAL_IP'];
                } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                    // Standard proxy header (can contain multiple IPs)
                    $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
                    $ip = trim($ips[0]); // First IP is the original client
                } elseif (!empty($_SERVER['HTTP_CLIENT_IP'])) {
                    // Some proxies use this
                    $ip = $_SERVER['HTTP_CLIENT_IP'];
                } elseif (!empty($_SERVER['REMOTE_ADDR'])) {
                    // Direct connection (no proxy)
                    $ip = $_SERVER['REMOTE_ADDR'];
                }

                // Method 2: Try Laravel's request helper (for web requests)
                if (!$ip && function_exists('request')) {
                    try {
                        $ip = request()->ip();
                    } catch (\Exception $e) {
                        // request() not available (CLI mode)
                    }
                }

                // Method 3: Check for development IP override (for local testing)
                if (!$ip || $ip === '127.0.0.1' || $ip === '::1') {
                    // Get from config (can be set in .env as FLUSSONIC_DEV_IP)
                    $devIp = config('services.flussonic.dev_ip');
                    if ($devIp && config('app.env') === 'local') {
                        $ip = $devIp;
                        Log::info('Flussonic: Using dev IP for local testing', ['ip' => $ip]);
                    }
                }

                // Final fallback
                $ip = $ip ?: '127.0.0.1';
            }

            // Calculate time range with desync allowance (for time difference between servers)
            $startTime = time() - $this->desync;
            $endTime = $startTime + $this->tokenLifetime;

            // Generate random salt
            $salt = bin2hex(random_bytes(16));

            // Generate token
            $token = $this->generateToken(
                streamName: $streamName,
                ipAddress: $ip,
                startTime: $startTime,
                endTime: $endTime,
                salt: $salt,
                userId: $userId
            );

            // Get protocol path
            $protocolPath = $this->protocols[$protocol] ?? $this->protocols['hls'];

            // Build full URL
            $url = sprintf(
                '%s/%s%s?token=%s&remote=%s',
                rtrim($this->baseUrl, '/'),
                $streamName,
                $protocolPath,
                $token,
                urlencode($ip)
            );

            Log::info('Flussonic: Stream URL generated', [
                'stream' => $streamName,
                'user_id' => $userId,
                'ip' => $ip,
                'start_time' => date('Y-m-d H:i:s', $startTime),
                'expires_at' => date('Y-m-d H:i:s', $endTime),
                'current_time' => date('Y-m-d H:i:s', time())
            ]);

            return [
                'url' => $url,
                'expires_at' => $endTime,
                'token' => $token,
                'protocol' => $protocol,
            ];
        } catch (\Exception $e) {
            Log::error('Flussonic: Failed to generate stream URL', [
                'stream' => $streamName,
                'error' => $e->getMessage()
            ]);

            throw new \Exception('فشل في توليد رابط البث: ' . $e->getMessage());
        }
    }

    /**
     * Generate secure token following Flussonic documentation
     *
     * Token format: sha1(string) + '-' + salt + '-' + endtime + '-' + starttime
     * String format: streamname + ip + starttime + endtime + secretkey + salt + user_id
     *
     * @param string $streamName
     * @param string $ipAddress
     * @param int $startTime
     * @param int $endTime
     * @param string $salt
     * @param int|null $userId
     * @return string
     */
    protected function generateToken(
        string $streamName,
        string $ipAddress,
        int $startTime,
        int $endTime,
        string $salt,
        ?int $userId = null
    ): string {
        // Build hash string according to Flussonic documentation
        $hashString = $streamName
            . $ipAddress
            . $startTime
            . $endTime
            . $this->secretKey
            . $salt
            . ($userId ? $userId : '');

        // Generate SHA1 hash
        $hash = sha1($hashString);

        // Build token: hash-salt-endtime-starttime
        $token = sprintf(
            '%s-%s-%s-%s',
            $hash,
            $salt,
            $endTime,
            $startTime
        );

        return $token;
    }

    /**
     * Check stream health status
     *
     * @param string $streamName
     * @param string|null $streamUrl Optional pre-generated stream URL with token
     * @return array ['status' => 'online|offline|unknown', 'info' => array|null]
     */
    public function checkStreamHealth(string $streamName, ?string $streamUrl = null): array
    {
        $cacheKey = "flussonic_health_{$streamName}";

        // Cache health check for 30 seconds (short cache to get fresh status)
        return Cache::remember($cacheKey, 30, function () use ($streamName, $streamUrl) {
            try {
                // If stream URL with token provided, use it directly
                // Otherwise try to check without auth (may fail on protected streams)
                $testUrl = $streamUrl ?? "{$this->baseUrl}/{$streamName}/index.m3u8";

                // Try HEAD request (lightweight, doesn't download content)
                $response = Http::timeout(5)
                    ->withOptions(['allow_redirects' => true])
                    ->head($testUrl);

                $statusCode = $response->status();

                if ($statusCode === 200) {
                    // Stream is online and accessible
                    return [
                        'status' => 'online',
                        'info' => ['http_code' => 200],
                        'checked_at' => now()->toDateTimeString()
                    ];
                } elseif ($statusCode === 503) {
                    // Stream exists but offline (no data)
                    return [
                        'status' => 'offline',
                        'info' => ['http_code' => 503],
                        'checked_at' => now()->toDateTimeString(),
                        'error' => 'Stream offline (no data source)'
                    ];
                } elseif ($statusCode === 403) {
                    // Authentication required - stream exists but need valid token
                    return [
                        'status' => 'unknown',
                        'info' => ['http_code' => 403],
                        'checked_at' => now()->toDateTimeString(),
                        'error' => 'Authentication required'
                    ];
                }

                return [
                    'status' => 'offline',
                    'info' => ['http_code' => $statusCode],
                    'checked_at' => now()->toDateTimeString(),
                    'error' => 'HTTP ' . $statusCode
                ];
            } catch (\Exception $e) {
                Log::warning('Flussonic: Health check failed', [
                    'stream' => $streamName,
                    'error' => $e->getMessage()
                ]);

                return [
                    'status' => 'unknown',
                    'info' => null,
                    'checked_at' => now()->toDateTimeString(),
                    'error' => $e->getMessage()
                ];
            }
        });
    }

    /**
     * Get stream information from Flussonic
     *
     * @param string $streamName
     * @return array|null
     */
    public function getStreamInfo(string $streamName): ?array
    {
        try {
            $response = Http::timeout(5)
                ->get("{$this->baseUrl}/{$streamName}/media-info");

            if ($response->successful()) {
                return $response->json();
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Flussonic: Failed to get stream info', [
                'stream' => $streamName,
                'error' => $e->getMessage()
            ]);

            return null;
        }
    }

    /**
     * Test connection to Flussonic server
     *
     * @return array ['success' => bool, 'message' => string]
     */
    public function testConnection(): array
    {
        try {
            $response = Http::timeout(5)->get($this->baseUrl);

            if ($response->successful() || $response->status() === 404) {
                // 404 is OK - it means server is responding
                return [
                    'success' => true,
                    'message' => 'تم الاتصال بسيرفر Flussonic بنجاح'
                ];
            }

            return [
                'success' => false,
                'message' => 'فشل الاتصال بسيرفر Flussonic: HTTP ' . $response->status()
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'فشل الاتصال بسيرفر Flussonic: ' . $e->getMessage()
            ];
        }
    }
}
