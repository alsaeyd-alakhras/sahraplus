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
        Schema::create('user_ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete(); // معرف المستخدم
            $table->foreignId('profile_id')->nullable()->constrained('user_profiles')->cascadeOnDelete(); // معرف الملف الشخصي
            $table->enum('content_type', ['movie', 'series', 'episode']); // نوع المحتوى
            $table->bigInteger('content_id'); // معرف المحتوى
            $table->decimal('rating', 2, 1); // التقييم (1.0 إلى 5.0)
            $table->text('review')->nullable(); // المراجعة النصية
            $table->boolean('is_spoiler')->default(false); // يحتوي على حرق أحداث
            $table->integer('helpful_count')->default(0); // عدد الأشخاص الذين وجدوها مفيدة
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('approved'); // حالة الموافقة
            $table->timestamp('reviewed_at')->useCurrent(); // تاريخ المراجعة
            $table->timestamps(); // تواريخ الإنشاء والتحديث

            $table->unique(['user_id', 'content_type', 'content_id']); // مراجعة واحدة لكل مستخدم
            $table->index(['content_type', 'content_id', 'status']); // فهرس للبحث
            $table->index(['rating', 'status']); // فهرس للتقييمات
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_ratings');
    }
};
