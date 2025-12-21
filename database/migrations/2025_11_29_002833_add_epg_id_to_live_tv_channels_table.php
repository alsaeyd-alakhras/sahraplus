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
            $table->string('epg_id', 50)->nullable()->after('stream_url')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('live_tv_channels', function (Blueprint $table) {
            $table->dropIndex(['epg_id']);
            $table->dropColumn('epg_id');
        });
    }
};
