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
        Schema::create('downloads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete(); // معرف المستخدم
            $table->foreignId('profile_id')->constrained('user_profiles')->cascadeOnDelete(); // معرف الملف الشخصي
            $table->enum('content_type', ['movie', 'episode']); // نوع المحتوى
            $table->bigInteger('content_id'); // معرف المحتوى
            $table->string('quality', 10); // جودة التحميل
            $table->string('format', 10); // تنسيق الملف
            $table->bigInteger('file_size')->nullable(); // حجم الملف بالبايت
            $table->enum('status', ['pending', 'downloading', 'completed', 'failed', 'expired'])->default('pending'); // حالة التحميل
            $table->integer('progress_percentage')->default(0); // نسبة تقدم التحميل
            $table->string('device_id', 100); // معرف الجهاز
            $table->string('download_token', 100)->unique(); // رمز التحميل الآمن
            $table->timestamp('expires_at')->nullable(); // تاريخ انتهاء الصلاحية
            $table->timestamp('completed_at')->nullable(); // تاريخ الإكمال
            $table->timestamps(); // تواريخ الإنشاء والتحديث

            $table->index(['user_id', 'profile_id', 'device_id']); // فهرس للمستخدم والجهاز
            $table->index(['status', 'expires_at']); // فهرس للحالة والانتهاء
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('downloads');
    }
};
