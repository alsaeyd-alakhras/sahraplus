<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('channel_programs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('channel_id')
                ->constrained('live_tv_channels')
                ->cascadeOnDelete();
            $table->string('title_ar', 200);
            $table->string('title_en', 200)->nullable();
            $table->text('description_ar')->nullable();
            $table->text('description_en')->nullable();
            $table->timestamp('start_time');
            $table->timestamp('end_time');
            $table->integer('duration_minutes');
            $table->string('genre', 50)->nullable();
            $table->boolean('is_live')->default(false);
            $table->boolean('is_repeat')->default(false);
            $table->string('poster_url', 255)->nullable();
            $table->timestamps();

            $table->index(['channel_id', 'start_time']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('channel_programs');
    }
};


