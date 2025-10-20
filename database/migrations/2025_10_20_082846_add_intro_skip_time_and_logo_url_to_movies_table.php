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
        Schema::table('movies', function (Blueprint $table) {
            $table->integer('intro_skip_time')->default(0)->after('duration_minutes');
            $table->string('logo_url')->nullable()->after('poster_url');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('movies', function (Blueprint $table) {
              $table->dropColumn(['intro_skip_time', 'logo_url']);
        });
    }
};
