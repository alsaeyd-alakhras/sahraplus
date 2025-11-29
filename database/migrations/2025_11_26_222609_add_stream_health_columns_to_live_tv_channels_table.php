<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('live_tv_channels', function (Blueprint $table) {
            $table->enum('stream_health_status', ['online', 'offline', 'unknown'])
                ->default('unknown')
                ->after('stream_type')
                ->comment('Current stream health status');

            $table->timestamp('stream_health_last_check')
                ->nullable()
                ->after('stream_health_status')
                ->comment('Last time stream health was checked');

            $table->text('stream_health_details')
                ->nullable()
                ->after('stream_health_last_check')
                ->comment('JSON details of last health check');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('live_tv_channels', function (Blueprint $table) {
            $table->dropColumn([
                'stream_health_status',
                'stream_health_last_check',
                'stream_health_details'
            ]);
        });
    }
};
