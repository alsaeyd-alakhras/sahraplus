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
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->enum('commentable_type', ['movie', 'series', 'episode', 'short']); // نوع المحتوى المعلق عليه
            $table->bigInteger('commentable_id'); // معرف المحتوى
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete(); // معرف المستخدم
            $table->foreignId('profile_id')->nullable()->constrained('user_profiles')->cascadeOnDelete(); // معرف الملف الشخصي
            $table->foreignId('parent_id')->nullable()->constrained('comments')->cascadeOnDelete(); // معرف التعليق الأب (للردود)
            $table->text('content'); // محتوى التعليق
            $table->integer('likes_count')->default(0); // عدد الإعجابات
            $table->integer('replies_count')->default(0); // عدد الردود
            $table->boolean('is_edited')->default(false); // تم تعديله
            $table->enum('status', ['pending', 'approved', 'rejected', 'hidden'])->default('approved'); // حالة الموافقة
            $table->timestamp('edited_at')->nullable(); // تاريخ آخر تعديل
            $table->timestamps(); // تواريخ الإنشاء والتحديث
            $table->index(['commentable_type', 'commentable_id']); // فهرس للبحث السريع
            $table->index(['parent_id']); // فهرس للردود
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};
