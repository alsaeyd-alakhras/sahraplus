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
        Schema::create('subtitles', function (Blueprint $table) {
            $table->id();
            $table->enum('content_type', ['movie', 'episode', 'short']); // نوع المحتوى
            $table->bigInteger('content_id'); // معرف المحتوى
            $table->string('language', 5); // لغة الترجمة
            $table->string('label', 50); // تسمية الترجمة للعرض
            $table->text('file_url'); // رابط ملف الترجمة
            $table->enum('format', ['vtt', 'srt', 'ass'])->default('vtt'); // تنسيق الترجمة
            $table->boolean('is_default')->default(false); // الترجمة الافتراضية
            $table->boolean('is_forced')->default(false); // ترجمة إجبارية
            $table->boolean('is_active')->default(true); // حالة النشاط
            $table->timestamps(); // تواريخ الإنشاء والتحديث
            $table->index(['content_type', 'content_id']); // فهرس للبحث السريع
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subtitles');
    }
};
