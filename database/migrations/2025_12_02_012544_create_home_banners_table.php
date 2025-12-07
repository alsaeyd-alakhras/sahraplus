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
        Schema::create('home_banners', function (Blueprint $table) {
            $table->id();
            $table->string('content_type', 20)->comment('نوع المحتوى: movie أو series');
            $table->unsignedBigInteger('content_id')->comment('معرف المحتوى في جدول movies أو series');
            $table->string('placement', 30)->default('mobile_banner')->comment('مكان العرض: frontend_slider أو mobile_banner');
            $table->boolean('is_kids')->default(false)->comment('محتوى للأطفال');
            $table->boolean('is_active')->default(true)->comment('حالة النشاط');
            $table->integer('sort_order')->default(0)->comment('ترتيب العرض');
            $table->timestamp('starts_at')->nullable()->comment('تاريخ بداية العرض (اختياري)');
            $table->timestamp('ends_at')->nullable()->comment('تاريخ نهاية العرض (اختياري)');
            $table->timestamps();
            
            // Indexes للأداء
            $table->index(['placement', 'is_active', 'is_kids']);
            $table->index(['content_type', 'content_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('home_banners');
    }
};
