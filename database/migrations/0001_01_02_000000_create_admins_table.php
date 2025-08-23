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
        Schema::create('admins', function (Blueprint $table) {
            $table->id(); // المعرف الفريد
            $table->string('name', 100); // الاسم الكامل
            $table->string('username', 50)->unique()->nullable(); // اسم المستخدم
            $table->string('password'); // كلمة المرور مشفرة
            $table->string('email', 191)->unique(); // البريد الإلكتروني
            $table->timestamp('email_verified_at')->nullable(); // تاريخ تأكيد البريد
            $table->rememberToken(); // رمز التذكر
            $table->text('avatar')->nullable(); // صورة الملف الشخصي
            $table->boolean('super_admin')->default(false); // صلاحية المشرف العام
            $table->boolean('is_active')->default(true); // حالة النشاط
            $table->timestamp('last_activity')->nullable(); // آخر نشاط
            $table->timestamps(); // تواريخ الإنشاء والتحديث
        });

        Schema::create('password_reset_tokens_admin', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admins');
        Schema::dropIfExists('password_reset_tokens_admin');
    }
};
