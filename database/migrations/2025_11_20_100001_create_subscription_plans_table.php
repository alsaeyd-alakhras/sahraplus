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
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name_ar', 100);
            $table->string('name_en', 100);
            $table->string('slug', 100)->unique();
            $table->text('description_ar')->nullable();
            $table->text('description_en')->nullable();
            $table->decimal('price', 10, 2);
            $table->string('currency', 3)->default('USD');
            $table->enum('billing_period', ['monthly', 'quarterly', 'yearly']);
            $table->integer('trial_days')->default(0);
            $table->integer('max_profiles')->default(1);
            $table->integer('max_devices')->default(1);
            $table->enum('video_quality', ['sd', 'hd', 'uhd'])->default('hd');
            $table->boolean('download_enabled')->default(false);
            $table->boolean('ads_enabled')->default(true);
            $table->boolean('live_tv_enabled')->default(false);
            $table->json('features')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_popular')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_plans');
    }
};