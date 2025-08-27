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
        Schema::create('shorts', function (Blueprint $table) {
            $table->id();
            $table->string('title', 200); // عنوان الفيديو القصير
            $table->text('description')->nullable(); // وصف الفيديو
            $table->string('video_path', 191); // مسار ملف الفيديو
            $table->string('poster_path', 191)->nullable(); // مسار صورة البوستر
            $table->enum('aspect_ratio', ['vertical', 'horizontal'])->default('vertical'); // نسبة العرض للارتفاع
            $table->unsignedBigInteger('likes_count')->default(0); // عدد الإعجابات
            $table->unsignedBigInteger('comments_count')->default(0); // عدد التعليقات
            $table->unsignedBigInteger('shares_count')->default(0); // عدد المشاركات
            $table->string('share_url', 191)->nullable(); // رابط المشاركة
            $table->boolean('is_featured')->default(false); // مميز
            $table->enum('status', ['active', 'inactive'])->default('active'); // حالة النشر
            $table->foreignId('created_by')->nullable()->constrained('admins')->nullOnDelete(); // منشئ المحتوى
            $table->timestamps(); // تواريخ الإنشاء والتحديث
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shorts');
    }
};
