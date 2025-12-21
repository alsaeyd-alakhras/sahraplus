<?php

namespace Tests\Feature\API;

use App\Models\ChannelProgram;
use App\Models\LiveTvCategory;
use App\Models\LiveTvChannel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Carbon\Carbon;

class LiveTvApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Configure Flussonic service for tests to avoid null config issues
        config([
            'services.flussonic.base_url' => 'http://flussonic.test',
            'services.flussonic.secret_key' => 'test-secret',
            'services.flussonic.token_lifetime' => 3600,
            'services.flussonic.desync' => 0,
            'services.flussonic.protocols' => [
                'hls' => '/index.m3u8',
                'dash' => '/manifest.mpd',
                'rtmp' => '',
            ],
        ]);
    }

    /** @test */
    public function it_lists_live_tv_categories()
    {
        LiveTvCategory::factory()->create([
            'name_ar' => 'إخبارية',
            'name_en' => 'News',
            'is_active' => true,
        ]);

        LiveTvCategory::factory()->create([
            'name_ar' => 'غير فعّالة',
            'name_en' => 'Inactive',
            'is_active' => false,
        ]);

        $response = $this->getJson('/api/v1/live-tv/categories');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonCount(1, 'data')
            ->assertJsonFragment([
                'name_ar' => 'إخبارية',
                'name_en' => 'News',
            ]);
    }

    /** @test */
    public function it_lists_live_tv_channels_and_supports_filters()
    {
        $category1 = LiveTvCategory::factory()->create();
        $category2 = LiveTvCategory::factory()->create();

        $channel1 = LiveTvChannel::factory()->create([
            'category_id' => $category1->id,
            'country' => 'SA',
            'language' => 'ar',
            'is_active' => true,
        ]);

        // Other combinations that should be filtered out in some cases
        LiveTvChannel::factory()->create([
            'category_id' => $category2->id,
            'country' => 'EG',
            'language' => 'ar',
            'is_active' => true,
        ]);

        LiveTvChannel::factory()->create([
            'category_id' => $category1->id,
            'country' => 'SA',
            'language' => 'en',
            'is_active' => false,
        ]);

        // Basic list
        $this->getJson('/api/v1/live-tv/channels')
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonFragment([
                'id' => $channel1->id,
            ]);

        // Filter by category
        $this->getJson('/api/v1/live-tv/channels?category_id=' . $category1->id)
            ->assertStatus(200)
            ->assertJsonFragment([
                'id' => $channel1->id,
            ]);

        // Filter by country + language
        $this->getJson('/api/v1/live-tv/channels?country=SA&language=ar')
            ->assertStatus(200)
            ->assertJsonFragment([
                'id' => $channel1->id,
            ]);
    }

    /** @test */
    public function it_returns_channels_by_category()
    {
        $category = LiveTvCategory::factory()->create();
        $channel = LiveTvChannel::factory()->create([
            'category_id' => $category->id,
            'is_active' => true,
        ]);

        LiveTvChannel::factory()->create([
            'is_active' => true,
        ]);

        $response = $this->getJson("/api/v1/live-tv/categories/{$category->id}/channels");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonFragment([
                'id' => $channel->id,
            ]);
    }

    /** @test */
    public function it_returns_404_if_category_not_found_in_channelsByCategory()
    {
        $this->getJson('/api/v1/live-tv/categories/9999/channels')
            ->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'Category not found',
            ]);
    }

    /** @test */
    public function it_shows_single_channel_by_slug()
    {
        $channel = LiveTvChannel::factory()->create([
            'slug' => 'test-channel',
        ]);

        $this->getJson('/api/v1/live-tv/channels/test-channel')
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonFragment([
                'id' => $channel->id,
                'slug' => 'test-channel',
            ]);
    }

    /** @test */
    public function it_returns_404_when_channel_slug_not_found()
    {
        $this->getJson('/api/v1/live-tv/channels/unknown-slug')
            ->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'Channel not found',
            ]);
    }

    /** @test */
    public function it_increments_viewer_count_on_watch()
    {
        $channel = LiveTvChannel::factory()->create([
            'viewer_count' => 5,
        ]);

        $response = $this->postJson("/api/v1/live-tv/channels/{$channel->id}/watch");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Viewer count updated successfully',
            ]);

        $channel->refresh();
        $this->assertEquals(6, $channel->viewer_count);
    }

    /** @test */
    public function it_returns_stream_data_for_channel()
    {
        $channel = LiveTvChannel::factory()->create([
            'stream_url' => 'test-stream',
            'stream_type' => 'hls',
        ]);

        $response = $this->getJson("/api/v1/live-tv/channels/{$channel->id}/stream");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonFragment([
                'id' => $channel->id,
                'stream_name' => 'test-stream',
                'stream_type' => 'hls',
            ])
            ->assertJsonStructure([
                'success',
                'data' => [
                    'id',
                    'name_ar',
                    'name_en',
                    'slug',
                    'stream_name',
                    'stream_url',
                    'stream_type',
                    'expires_at',
                    'expires_at_formatted',
                    'language',
                    'country',
                ],
            ]);
    }

    /** @test */
    public function it_returns_404_for_stream_if_channel_not_found()
    {
        $this->getJson('/api/v1/live-tv/channels/9999/stream')
            ->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'Channel not found',
            ]);
    }

    /** @test */
    public function it_returns_programs_for_channel()
    {
        $channel = LiveTvChannel::factory()->create();

        ChannelProgram::factory()->count(3)->create([
            'channel_id' => $channel->id,
        ]);

        $response = $this->getJson("/api/v1/live-tv/channels/{$channel->id}/programs");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonCount(3, 'data');
    }

    /** @test */
    public function it_returns_404_when_requesting_programs_for_unknown_channel()
    {
        $this->getJson('/api/v1/live-tv/channels/9999/programs')
            ->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'Channel not found',
            ]);
    }

    /** @test */
    public function it_returns_current_program_for_channel()
    {
        $channel = LiveTvChannel::factory()->create();

        $now = Carbon::now();

        $program = ChannelProgram::factory()->create([
            'channel_id' => $channel->id,
            'start_time' => $now->copy()->subMinutes(30),
            'end_time' => $now->copy()->addMinutes(30),
        ]);

        $response = $this->getJson("/api/v1/live-tv/channels/{$channel->id}/programs/current");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonFragment([
                'id' => $program->id,
            ]);
    }

    /** @test */
    public function it_returns_404_if_no_current_program_found()
    {
        $channel = LiveTvChannel::factory()->create();

        $this->getJson("/api/v1/live-tv/channels/{$channel->id}/programs/current")
            ->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'No current program found',
            ]);
    }

    /** @test */
    public function it_returns_upcoming_programs_for_channel()
    {
        $channel = LiveTvChannel::factory()->create();

        ChannelProgram::factory()->count(2)->create([
            'channel_id' => $channel->id,
            'start_time' => Carbon::now()->addHour(),
            'end_time' => Carbon::now()->addHours(2),
        ]);

        $response = $this->getJson("/api/v1/live-tv/channels/{$channel->id}/programs/upcoming");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonCount(2, 'data');
    }

    /** @test */
    public function it_returns_404_when_requesting_upcoming_programs_for_unknown_channel()
    {
        $this->getJson('/api/v1/live-tv/channels/9999/programs/upcoming')
            ->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'Channel not found',
            ]);
    }
}


