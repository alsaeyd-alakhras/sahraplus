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
        Schema::create('watch_progres', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete(); // معرف المستخدم
            $table->foreignId('profile_id')->constrained('user_profiles')->cascadeOnDelete(); // معرف الملف الشخصي
            $table->enum('content_type', ['movie', 'episode']); // نوع المحتوى (فيلم أو حلقة)
            $table->bigInteger('content_id'); // معرف المحتوى
            $table->integer('watched_seconds')->default(0); // الوقت المشاهد بالثواني
            $table->integer('total_seconds'); // إجمالي مدة المحتوى بالثواني
            $table->decimal('progress_percentage', 5, 2)->default(0); // نسبة التقدم
            $table->boolean('is_completed')->default(false); // مكتمل المشاهدة
            $table->timestamp('last_watched_at')->useCurrent(); // آخر وقت مشاهدة
            $table->timestamps(); // تواريخ الإنشاء والتحديث

            $table->unique(['profile_id', 'content_type', 'content_id']); // منع التكرار
            $table->index(['user_id', 'profile_id', 'is_completed']); // فهرس للبحث
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('watch_progres');
    }
};
