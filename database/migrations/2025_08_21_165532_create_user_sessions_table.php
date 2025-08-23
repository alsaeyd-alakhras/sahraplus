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
        Schema::create('user_sessions', function (Blueprint $table) {
            $table->id(); // المعرف الفريد
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete(); // معرف المستخدم
            $table->string('device_name', 100)->nullable(); // اسم الجهاز
            $table->enum('device_type', ['mobile', 'tablet', 'desktop', 'tv', 'browser']); // نوع الجهاز
            $table->string('platform', 50)->nullable(); // نظام التشغيل
            $table->string('ip_address', 45); // عنوان IP
            $table->text('user_agent')->nullable(); // معلومات المتصفح
            $table->string('session_token')->unique(); // رمز الجلسة
            $table->boolean('is_active')->default(true); // حالة النشاط
            $table->timestamp('last_activity')->nullable(); // آخر نشاط
            $table->timestamp('expires_at')->nullable(); // تاريخ انتهاء الصلاحية
            $table->timestamps(); // تواريخ الإنشاء والتحديث
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_sessions');
    }
};
