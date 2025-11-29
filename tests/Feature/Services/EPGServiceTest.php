<?php

namespace Tests\Feature\Services;

use App\Models\LiveTvChannel;
use App\Models\ChannelProgram;
use App\Services\EPGService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class EPGServiceTest extends TestCase
{
    use RefreshDatabase;

    protected EPGService $epgService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->epgService = app(EPGService::class);
    }

    /** @test */
    public function it_can_fetch_epg_from_url()
    {
        Http::fake([
            'example.com/epg.xml' => Http::response('<?xml version="1.0"?><tv></tv>', 200),
        ]);

        $content = $this->epgService->fetchEPGFromUrl('https://example.com/epg.xml', 'xmltv');

        $this->assertStringContainsString('<tv>', $content);
    }

    /** @test */
    public function it_throws_exception_when_fetch_fails()
    {
        Http::fake([
            'example.com/epg.xml' => Http::response('', 500),
        ]);

        $this->expectException(\Exception::class);
        $this->epgService->fetchEPGFromUrl('https://example.com/epg.xml', 'xmltv');
    }

    /** @test */
    public function it_can_parse_xmltv_format()
    {
        $xmlContent = '<?xml version="1.0" encoding="UTF-8"?>
<tv>
    <programme start="20251126120000 +0300" stop="20251126130000 +0300" channel="test-channel">
        <title lang="ar">برنامج اختبار</title>
        <title lang="en">Test Program</title>
        <desc lang="ar">وصف البرنامج</desc>
        <desc lang="en">Program description</desc>
        <category>News</category>
        <icon src="https://example.com/icon.jpg"/>
    </programme>
</tv>';

        $programs = $this->epgService->parseXMLTV($xmlContent);

        $this->assertArrayHasKey('test-channel', $programs);
        $this->assertCount(1, $programs['test-channel']);
        $this->assertEquals('برنامج اختبار', $programs['test-channel'][0]['title_ar']);
        $this->assertEquals('Test Program', $programs['test-channel'][0]['title_en']);
        $this->assertEquals('وصف البرنامج', $programs['test-channel'][0]['description_ar']);
        $this->assertNotNull($programs['test-channel'][0]['start_time']);
        $this->assertNotNull($programs['test-channel'][0]['end_time']);
    }

    /** @test */
    public function it_maps_genre_correctly()
    {
        $xmlContent = '<?xml version="1.0" encoding="UTF-8"?>
<tv>
    <programme start="20251126120000 +0300" stop="20251126130000 +0300" channel="ch1">
        <title lang="en">News Program</title>
        <category>News</category>
    </programme>
    <programme start="20251126130000 +0300" stop="20251126140000 +0300" channel="ch1">
        <title lang="en">Sports Show</title>
        <category>Sport</category>
    </programme>
    <programme start="20251126140000 +0300" stop="20251126150000 +0300" channel="ch1">
        <title lang="en">Unknown Type</title>
        <category>Mystery</category>
    </programme>
</tv>';

        $programs = $this->epgService->parseXMLTV($xmlContent);

        // Test genre mapping
        $channel = LiveTvChannel::factory()->create(['stream_url' => 'ch1']);
        $this->epgService->syncChannelPrograms($channel, 'ch1', $programs['ch1']);

        $this->assertDatabaseHas('channel_programs', [
            'channel_id' => $channel->id,
            'title_en' => 'News Program',
            'genre' => 'news',
        ]);

        $this->assertDatabaseHas('channel_programs', [
            'channel_id' => $channel->id,
            'title_en' => 'Sports Show',
            'genre' => 'sports',
        ]);

        // Test "others" for unknown genre
        $this->assertDatabaseHas('channel_programs', [
            'channel_id' => $channel->id,
            'title_en' => 'Unknown Type',
            'genre' => 'others',
        ]);
    }

    /** @test */
    public function it_maps_null_genre_to_others()
    {
        $xmlContent = '<?xml version="1.0" encoding="UTF-8"?>
<tv>
    <programme start="20251126120000 +0300" stop="20251126130000 +0300" channel="ch1">
        <title lang="en">No Genre Program</title>
    </programme>
</tv>';

        $programs = $this->epgService->parseXMLTV($xmlContent);
        $channel = LiveTvChannel::factory()->create(['stream_url' => 'ch1']);
        
        $this->epgService->syncChannelPrograms($channel, 'ch1', $programs['ch1']);

        $this->assertDatabaseHas('channel_programs', [
            'channel_id' => $channel->id,
            'title_en' => 'No Genre Program',
            'genre' => 'others',
        ]);
    }

    /** @test */
    public function it_can_sync_channel_programs()
    {
        $channel = LiveTvChannel::factory()->create(['stream_url' => 'test-ch']);

        $programs = [
            [
                'title_ar' => 'برنامج 1',
                'title_en' => 'Program 1',
                'description_ar' => null,
                'description_en' => null,
                'start_time' => now(),
                'end_time' => now()->addHour(),
                'genre' => 'news',
                'icon' => null,
            ],
            [
                'title_ar' => 'برنامج 2',
                'title_en' => 'Program 2',
                'description_ar' => null,
                'description_en' => null,
                'start_time' => now()->addHour(),
                'end_time' => now()->addHours(2),
                'genre' => 'sports',
                'icon' => null,
            ],
        ];

        $synced = $this->epgService->syncChannelPrograms($channel, 'test-ch', $programs);

        $this->assertEquals(2, $synced);
        $this->assertDatabaseCount('channel_programs', 2);
    }

    /** @test */
    public function it_skips_duplicate_programs()
    {
        $channel = LiveTvChannel::factory()->create(['stream_url' => 'test-ch']);
        $startTime = now();
        $endTime = now()->addHour();

        // Create existing program
        ChannelProgram::factory()->create([
            'channel_id' => $channel->id,
            'start_time' => $startTime,
            'end_time' => $endTime,
        ]);

        $programs = [
            [
                'title_ar' => 'برنامج مكرر',
                'title_en' => 'Duplicate Program',
                'description_ar' => null,
                'description_en' => null,
                'start_time' => $startTime,
                'end_time' => $endTime,
                'genre' => 'news',
                'icon' => null,
            ],
        ];

        $synced = $this->epgService->syncChannelPrograms($channel, 'test-ch', $programs);

        $this->assertEquals(0, $synced); // Should skip duplicate
        $this->assertDatabaseCount('channel_programs', 1); // Still only one
    }

    /** @test */
    public function it_can_clean_old_programs()
    {
        // Create old program (10 days ago)
        ChannelProgram::factory()->create([
            'start_time' => now()->subDays(10),
            'end_time' => now()->subDays(10)->addHour(),
        ]);

        // Create recent program (3 days ago)
        ChannelProgram::factory()->create([
            'start_time' => now()->subDays(3),
            'end_time' => now()->subDays(3)->addHour(),
        ]);

        // Create future program
        ChannelProgram::factory()->create([
            'start_time' => now()->addDay(),
            'end_time' => now()->addDay()->addHour(),
        ]);

        $deleted = $this->epgService->cleanOldPrograms(7);

        $this->assertEquals(1, $deleted); // Only the 10-day old one
        $this->assertDatabaseCount('channel_programs', 2); // 2 remaining
    }

    /** @test */
    public function it_calculates_duration_correctly()
    {
        $channel = LiveTvChannel::factory()->create();
        $startTime = now();
        $endTime = now()->addMinutes(45);

        $programs = [
            [
                'title_ar' => 'برنامج',
                'title_en' => 'Program',
                'description_ar' => null,
                'description_en' => null,
                'start_time' => $startTime,
                'end_time' => $endTime,
                'genre' => 'news',
                'icon' => null,
            ],
        ];

        $this->epgService->syncChannelPrograms($channel, 'ch', $programs);

        $program = ChannelProgram::first();
        $this->assertEquals(45, $program->duration_minutes);
    }

    /** @test */
    public function it_handles_various_genre_variations()
    {
        $variations = [
            'News' => 'news',
            'NEWS' => 'news',
            'Sport' => 'sports',
            'Sports' => 'sports',
            'Movie' => 'drama',
            'Drama' => 'drama',
            'Kids' => 'kids',
            'Children' => 'kids',
            'Religion' => 'religious',
            'Religious' => 'religious',
            'Education' => 'educational',
            'Educational' => 'educational',
            'Variety' => 'entertainment',
            'Entertainment' => 'entertainment',
            'Unknown' => 'others',
            'Random' => 'others',
            '' => 'others',
        ];

        foreach ($variations as $input => $expected) {
            $reflection = new \ReflectionClass($this->epgService);
            $method = $reflection->getMethod('mapGenre');
            $method->setAccessible(true);

            $result = $method->invoke($this->epgService, $input);
            $this->assertEquals($expected, $result, "Failed for input: {$input}");
        }
    }
}
