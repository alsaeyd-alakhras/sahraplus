<?php

namespace App\Services;

use App\Models\ChannelProgram;
use App\Models\LiveTvChannel;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class EPGService
{
    /**
     * Fetch EPG data from external URL
     *
     * @param string $url URL to EPG source (XMLTV or JSON)
     * @param string $format Format: 'xmltv' or 'json'
     * @return string|array Raw EPG data
     * @throws \Exception
     */
    public function fetchEPGFromUrl(string $url, string $format = 'xmltv')
    {
        try {
            Log::info('EPGService: Fetching EPG data', ['url' => $url, 'format' => $format]);

            $response = Http::timeout(120)->get($url);

            if (!$response->successful()) {
                throw new \Exception("Failed to fetch EPG: HTTP {$response->status()}");
            }

            $data = $format === 'json' ? $response->json() : $response->body();

            Log::info('EPGService: EPG data fetched successfully', [
                'size' => strlen($data),
                'format' => $format,
            ]);

            return $data;
        } catch (\Exception $e) {
            Log::error('EPGService: Failed to fetch EPG', [
                'url' => $url,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Parse XMLTV format EPG data
     *
     * @param string $xmlContent XMLTV content
     * @return array Parsed programs grouped by channel
     */
    public function parseXMLTV(string $xmlContent): array
    {
        try {
            $xml = simplexml_load_string($xmlContent);

            if ($xml === false) {
                throw new \Exception('Invalid XML format');
            }

            $programs = [];

            foreach ($xml->programme as $programme) {
                $channelId = (string) $programme['channel'];
                $start = $this->parseXMLTVTime((string) $programme['start']);
                $stop = $this->parseXMLTVTime((string) $programme['stop']);

                $title = [];
                foreach ($programme->title as $t) {
                    $lang = (string) $t['lang'] ?? 'en';
                    $title[$lang] = (string) $t;
                }

                $desc = [];
                foreach ($programme->desc as $d) {
                    $lang = isset($d['lang']) ? (string) $d['lang'] : 'en';
                    $desc[$lang] = (string) $d;
                }

                $category = [];
                foreach ($programme->category as $cat) {
                    $category[] = (string) $cat;
                }

                // Get first available title as fallback
                $fallbackTitle = !empty($title) ? reset($title) : 'Unknown';
                $fallbackDesc = !empty($desc) ? reset($desc) : null;

                $programs[$channelId][] = [
                    'title_ar' => $title['ar'] ?? $fallbackTitle,
                    'title_en' => $title['en'] ?? $fallbackTitle,
                    'description_ar' => $desc['ar'] ?? $fallbackDesc,
                    'description_en' => $desc['en'] ?? $fallbackDesc,
                    'start_time' => $start,
                    'end_time' => $stop,
                    'genre' => $category[0] ?? null,
                    'icon' => isset($programme->icon) ? (string) $programme->icon['src'] : null,
                ];
            }

            Log::info('EPGService: XMLTV parsed successfully', [
                'channels' => count($programs),
                'total_programs' => array_sum(array_map('count', $programs)),
            ]);

            return $programs;
        } catch (\Exception $e) {
            Log::error('EPGService: Failed to parse XMLTV', [
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Parse XMLTV datetime format (YYYYMMDDHHmmss +offset)
     *
     * @param string $xmltvTime
     * @return Carbon
     */
    protected function parseXMLTVTime(string $xmltvTime): Carbon
    {
        // Format: 20231125120000 +0300
        preg_match('/(\d{14})\s*([+-]\d{4})?/', $xmltvTime, $matches);

        $dateStr = $matches[1];
        $timezone = $matches[2] ?? '+0000';

        return Carbon::createFromFormat('YmdHis', $dateStr, $timezone);
    }

    /**
     * Sync channel programs from parsed EPG data
     *
     * @param LiveTvChannel $channel
     * @param string $externalChannelId Channel ID from EPG source
     * @param array $programs Array of program data
     * @return int Number of programs synced
     */
    public function syncChannelPrograms(LiveTvChannel $channel, string $externalChannelId, array $programs): int
    {
        try {
            $synced = 0;
            $skipped = 0;

            foreach ($programs as $programData) {
                // Check if program already exists
                $exists = ChannelProgram::where('channel_id', $channel->id)
                    ->where('start_time', $programData['start_time'])
                    ->where('end_time', $programData['end_time'])
                    ->exists();

                if ($exists) {
                    $skipped++;
                    continue;
                }

                // Calculate duration
                $duration = $programData['start_time']->diffInMinutes($programData['end_time']);

                // Map genre to our system
                $genre = $this->mapGenre($programData['genre']);

                ChannelProgram::create([
                    'channel_id' => $channel->id,
                    'title_ar' => $programData['title_ar'],
                    'title_en' => $programData['title_en'],
                    'description_ar' => $programData['description_ar'],
                    'description_en' => $programData['description_en'],
                    'start_time' => $programData['start_time'],
                    'end_time' => $programData['end_time'],
                    'duration_minutes' => $duration,
                    'genre' => $genre,
                    'poster_url' => $programData['icon'] ?? null,
                    'is_live' => false,
                    'is_repeat' => false,
                ]);

                $synced++;
            }

            Log::info('EPGService: Programs synced', [
                'channel_id' => $channel->id,
                'channel_name' => $channel->name_ar,
                'synced' => $synced,
                'skipped' => $skipped,
            ]);

            return $synced;
        } catch (\Exception $e) {
            Log::error('EPGService: Failed to sync programs', [
                'channel_id' => $channel->id,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Map external genre to our system genres
     *
     * @param string|null $externalGenre
     * @return string
     */
    protected function mapGenre(?string $externalGenre): string
    {
        if (!$externalGenre) {
            return 'others';
        }

        $genreMap = [
            'news' => 'news',
            'sports' => 'sports',
            'sport' => 'sports',
            'drama' => 'drama',
            'movie' => 'drama',
            'documentary' => 'documentary',
            'entertainment' => 'entertainment',
            'variety' => 'entertainment',
            'kids' => 'kids',
            'children' => 'kids',
            'religious' => 'religious',
            'religion' => 'religious',
            'educational' => 'educational',
            'education' => 'educational',
        ];

        $normalized = strtolower(trim($externalGenre));

        return $genreMap[$normalized] ?? 'others';
    }

    /**
     * Update daily schedule for all channels using epg_id
     *
     * @param string $epgUrl URL to EPG source (default: https://epg.pw/xmltv/epg_lite.xml)
     * @return array Summary of sync results
     */
    public function updateDailySchedule(string $epgUrl = 'https://epg.pw/xmltv/epg_lite.xml'): array
    {
        try {
            // Fetch EPG data from URL
            Log::info('EPGService: Starting daily schedule update', ['url' => $epgUrl]);

            $xmlContent = $this->fetchEPGFromUrl($epgUrl, 'xmltv');

            // Parse XMLTV
            $allPrograms = $this->parseXMLTV($xmlContent);

            $results = [
                'total_channels' => 0,
                'synced_channels' => 0,
                'total_programs' => 0,
                'skipped_channels' => 0,
                'errors' => [],
            ];

            // Get all channels that have epg_id
            $channels = LiveTvChannel::whereNotNull('epg_id')
                ->where('is_active', true)
                ->get();

            Log::info('EPGService: Found channels with EPG ID', ['count' => $channels->count()]);

            foreach ($channels as $channel) {
                $results['total_channels']++;

                // Check if EPG data exists for this channel
                if (!isset($allPrograms[$channel->epg_id])) {
                    $results['skipped_channels']++;
                    $results['errors'][] = "No EPG data for channel: {$channel->name_ar} (EPG ID: {$channel->epg_id})";
                    continue;
                }

                try {
                    $synced = $this->syncChannelPrograms(
                        $channel,
                        $channel->epg_id,
                        $allPrograms[$channel->epg_id]
                    );

                    $results['synced_channels']++;
                    $results['total_programs'] += $synced;
                } catch (\Exception $e) {
                    $results['errors'][] = "Failed to sync {$channel->name_ar}: {$e->getMessage()}";
                }
            }

            Log::info('EPGService: Daily schedule updated', $results);

            return $results;
        } catch (\Exception $e) {
            Log::error('EPGService: Failed to update daily schedule', [
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Clean old programs (older than specified days)
     *
     * @param int $daysToKeep Keep programs from last X days
     * @return int Number of programs deleted
     */
    public function cleanOldPrograms(int $daysToKeep = 7): int
    {
        $cutoffDate = Carbon::now()->subDays($daysToKeep);

        $deleted = ChannelProgram::where('end_time', '<', $cutoffDate)->delete();

        Log::info('EPGService: Old programs cleaned', [
            'cutoff_date' => $cutoffDate->toDateTimeString(),
            'deleted_count' => $deleted,
        ]);

        return $deleted;
    }

    /**
     * Sync programs for a single channel immediately
     *
     * @param LiveTvChannel $channel
     * @param string $epgUrl EPG source URL
     * @return array Results with synced count and errors
     */
    public function syncSingleChannelPrograms(LiveTvChannel $channel, string $epgUrl = 'https://epg.pw/xmltv/epg_lite.xml'): array
    {
        try {
            // Skip if channel has no epg_id
            if (empty($channel->epg_id)) {
                return [
                    'success' => false,
                    'message' => 'Channel has no EPG ID',
                    'synced' => 0,
                ];
            }

            Log::info('EPGService: Syncing single channel programs', [
                'channel_id' => $channel->id,
                'channel_name' => $channel->name_ar,
                'epg_id' => $channel->epg_id,
            ]);

            // Fetch and parse EPG
            $xmlContent = $this->fetchEPGFromUrl($epgUrl, 'xmltv');
            $allPrograms = $this->parseXMLTV($xmlContent);

            // Check if EPG data exists for this channel
            if (!isset($allPrograms[$channel->epg_id])) {
                Log::warning('EPGService: No EPG data found for channel', [
                    'channel_id' => $channel->id,
                    'epg_id' => $channel->epg_id,
                ]);

                return [
                    'success' => false,
                    'message' => 'No EPG data found for this channel',
                    'synced' => 0,
                ];
            }

            // Sync programs
            $synced = $this->syncChannelPrograms(
                $channel,
                $channel->epg_id,
                $allPrograms[$channel->epg_id]
            );

            Log::info('EPGService: Single channel sync completed', [
                'channel_id' => $channel->id,
                'synced' => $synced,
            ]);

            return [
                'success' => true,
                'message' => "Synced {$synced} programs",
                'synced' => $synced,
            ];
        } catch (\Exception $e) {
            Log::error('EPGService: Failed to sync single channel', [
                'channel_id' => $channel->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => $e->getMessage(),
                'synced' => 0,
            ];
        }
    }

    /**
     * Fetch EPG XML and cache channels list in database
     *
     * @param string $epgUrl EPG source URL
     * @return array Cached data with channels and metadata
     */
    public function fetchAndCacheChannelsList(string $epgUrl): array
    {
        try {
            Log::info('EPGService: Fetching and caching EPG channels list');

            $xmlContent = $this->fetchEPGFromUrl($epgUrl, 'xmltv');
            $xml = simplexml_load_string($xmlContent);

            if ($xml === false) {
                throw new \Exception('Invalid XML format');
            }

            $channels = [];

            foreach ($xml->channel as $channel) {
                $channelId = (string) $channel['id'];

                // Get display names
                $displayNames = [];
                foreach ($channel->{'display-name'} as $name) {
                    $displayNames[] = (string) $name;
                }

                // Get icon URL if available
                $iconUrl = null;
                if (isset($channel->icon['src'])) {
                    $iconUrl = (string) $channel->icon['src'];
                }

                $channels[] = [
                    'id' => $channelId,
                    'name' => $displayNames[0] ?? $channelId,
                    'display_names' => $displayNames,
                    'icon' => $iconUrl,
                ];
            }

            $cacheData = [
                'channels' => $channels,
                'updated_at' => now()->toDateTimeString(),
                'total_channels' => count($channels),
            ];

            // Save to system_settings table
            \App\Models\SystemSetting::setValue('epg_channels_cache', json_encode($cacheData));

            Log::info('EPGService: EPG channels list cached successfully', [
                'total_channels' => count($channels),
            ]);

            return $cacheData;
        } catch (\Exception $e) {
            Log::error('EPGService: Failed to fetch and cache EPG channels', [
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Get list of all available channels from database cache
     *
     * @return array List of channels with id and display-name
     */
    public function getAvailableChannels(): array
    {
        try {
            // Get from system_settings table
            $cachedJson = \App\Models\SystemSetting::getValue('epg_channels_cache');

            if (empty($cachedJson)) {
                Log::warning('EPGService: No EPG channels cache found in database');
                return [];
            }

            $cacheData = json_decode($cachedJson, true);

            if (!$cacheData || !isset($cacheData['channels'])) {
                Log::warning('EPGService: Invalid EPG channels cache format');
                return [];
            }

            Log::info('EPGService: Retrieved available channels from database cache', [
                'count' => count($cacheData['channels']),
                'updated_at' => $cacheData['updated_at'] ?? 'unknown',
            ]);

            return $cacheData['channels'];
        } catch (\Exception $e) {
            Log::error('EPGService: Failed to get available channels from cache', [
                'error' => $e->getMessage(),
            ]);

            return [];
        }
    }

    /**
     * Get channel details from EPG by channel ID
     *
     * @param string $channelId EPG channel ID
     * @return array|null Channel details or null if not found
     */
    public function getChannelDetails(string $channelId): ?array
    {
        $channels = $this->getAvailableChannels();

        foreach ($channels as $channel) {
            if ($channel['id'] === $channelId) {
                return $channel;
            }
        }

        return null;
    }
}
