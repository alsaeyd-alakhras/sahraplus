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
        Schema::create('tmdb_sync_logs', function (Blueprint $table) {
            $table->id();
            $table->enum('content_type', ['movie', 'series', 'person']); // نوع المحتوى
            $table->bigInteger('content_id')->nullable(); // معرف المحتوى المحلي
            $table->string('tmdb_id', 20); // معرف TMDB
            $table->enum('action', ['fetch', 'update', 'sync']); // نوع العملية
            $table->enum('status', ['success', 'failed', 'partial']); // حالة العملية
            $table->json('synced_data')->nullable(); // البيانات المزامنة
            $table->text('error_message')->nullable(); // رسالة الخطأ إن وجدت
            $table->timestamp('synced_at'); // تاريخ المزامنة
            $table->timestamps(); // تواريخ الإنشاء والتحديث
            $table->index(['content_type', 'tmdb_id']); // فهرس للبحث
            $table->index(['synced_at']); // فهرس للتواريخ

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tmdb_sync_logs');
    }
};
