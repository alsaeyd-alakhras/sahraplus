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
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id(); // المعرف الفريد
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete(); // معرف المستخدم
            $table->string('name', 100); // اسم الملف الشخصي
            $table->text('avatar_url')->nullable(); // صورة الملف الشخصي
            $table->boolean('is_default')->default(false); // الملف الافتراضي
            $table->boolean('is_child_profile')->default(false); // ملف الأطفال
            $table->integer('age_restriction')->default(18); // قيود العمر
            $table->string('pin_code', 6)->nullable(); // رمز الحماية
            $table->string('language', 5)->default('ar'); // اللغة المفضلة
            $table->boolean('is_active')->default(true); // حالة النشاط
            $table->timestamps(); // تواريخ الإنشاء والتحديث
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_profiles');
    }
};
