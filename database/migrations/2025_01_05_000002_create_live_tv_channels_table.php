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
        Schema::create('live_tv_channels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')
                ->constrained('live_tv_categories')
                ->cascadeOnDelete();
            $table->string('name_ar', 100);
            $table->string('name_en', 100);
            $table->string('slug', 100)->unique();
            $table->text('description_ar')->nullable();
            $table->text('description_en')->nullable();
            $table->string('logo_url', 255)->nullable();
            $table->string('poster_url', 255)->nullable();
            $table->string('stream_url', 500);
            $table->enum('stream_type', ['hls', 'dash', 'rtmp'])->default('hls');
            $table->integer('viewer_count')->default(0);
            $table->integer('sort_order')->default(0);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->string('language', 10)->default('ar');
            $table->string('country', 4)->nullable();
            $table->timestamps();

            $table->index(['category_id', 'is_active']);
            $table->index(['country', 'language']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('live_tv_channels');
    }
};


