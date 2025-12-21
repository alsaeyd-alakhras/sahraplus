<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Extract stream names from existing URLs before modifying column
        $channels = DB::table('live_tv_channels')->get();

        foreach ($channels as $channel) {
            $streamUrl = $channel->stream_url;

            // Extract stream name from URL patterns:
            // http://domain.com/stream_name/index.m3u8 -> stream_name
            // http://domain.com/stream_name -> stream_name
            if (preg_match('#/([^/]+?)(?:/index\.m3u8|/manifest\.mpd|/)?$#', $streamUrl, $matches)) {
                $streamName = $matches[1];
                DB::table('live_tv_channels')
                    ->where('id', $channel->id)
                    ->update(['stream_url' => $streamName]);
            }
        }

        // Modify column to varchar(100) - Flussonic stream name only
        Schema::table('live_tv_channels', function (Blueprint $table) {
            $table->string('stream_url', 100)->comment('Flussonic stream name only')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('live_tv_channels', function (Blueprint $table) {
            $table->string('stream_url', 500)->comment('')->change();
        });
    }
};
