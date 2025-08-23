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
        Schema::create('users', function (Blueprint $table) {
            $table->id(); // المعرف الفريد
            $table->string('first_name', 100); // الاسم الأول
            $table->string('last_name', 100)->nullable(); // الاسم الأخير
            $table->string('email')->unique(); // البريد الإلكتروني
            $table->timestamp('email_verified_at')->nullable(); // تاريخ تأكيد البريد
            $table->string('phone', 20)->nullable(); // رقم الهاتف
            $table->string('password'); // كلمة المرور مشفرة
            $table->date('date_of_birth')->nullable(); // تاريخ الميلاد
            $table->enum('gender', ['male', 'female'])->nullable(); // الجنس
            $table->string('country_code', 2)->nullable(); // رمز الدولة
            $table->string('language', 5)->default('ar'); // اللغة المفضلة
            $table->text('avatar')->nullable(); // رابط صورة المستخدم
            $table->boolean('is_active')->default(true); // حالة النشاط
            $table->boolean('is_banned')->default(false); // حالة الحظر
            $table->boolean('email_notifications')->default(true); // تفعيل إشعارات البريد
            $table->boolean('push_notifications')->default(true); // تفعيل الإشعارات المباشرة
            $table->boolean('parental_controls')->default(false); // تفعيل الرقابة الأبوية
            $table->timestamp('last_activity')->nullable(); // آخر نشاط
            $table->rememberToken(); // رمز التذكر للجلسات
            $table->timestamps(); // تواريخ الإنشاء والتحديث
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
