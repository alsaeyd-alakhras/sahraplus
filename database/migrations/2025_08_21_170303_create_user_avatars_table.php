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
        Schema::create('user_avatars', function (Blueprint $table) {
            $table->id(); // المعرف الفريد
            $table->string('name', 100); // اسم الأفاتار
            $table->text('image_url'); // رابط الصورة
            $table->enum('category', ['male', 'female', 'child', 'general'])->default('general'); // فئة الأفاتار
            $table->boolean('is_default')->default(false); // افتراضي للفئة
            $table->boolean('is_active')->default(true); // حالة النشاط
            $table->integer('sort_order')->default(0); // ترتيب العرض
            $table->timestamps(); // تواريخ الإنشاء والتحديث
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_avatars');
    }
};
