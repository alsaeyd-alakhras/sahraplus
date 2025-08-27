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
        Schema::create('video_files', function (Blueprint $table) {
            $table->id();
            $table->enum('content_type', ['movie', 'episode', 'short']); // نوع المحتوى
            $table->bigInteger('content_id'); // معرف المحتوى
            $table->enum('video_type', ['main', 'trailer', 'teaser', 'clip'])->default('main'); // نوع الفيديو
            $table->enum('quality', ['240p', '360p', '480p', '720p', '1080p', '4k']); // جودة الفيديو
            $table->enum('format', ['mp4', 'hls', 'm3u8', 'webm']); // تنسيق الفيديو
            $table->text('file_url'); // رابط الملف
            $table->bigInteger('file_size')->nullable(); // حجم الملف بالبايت
            $table->integer('duration_seconds')->nullable(); // مدة الفيديو بالثواني
            $table->boolean('is_downloadable')->default(false); // قابل للتحميل
            $table->boolean('is_active')->default(true); // حالة النشاط
            $table->timestamps(); // تواريخ الإنشاء والتحديث
            $table->index(['content_type', 'content_id']); // فهرس للبحث 

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('video_files');
    }
};
