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
        Schema::create('viewing_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete(); // معرف المستخدم
            $table->foreignId('profile_id')->constrained('user_profiles')->cascadeOnDelete(); // معرف الملف الشخصي
            $table->enum('content_type', ['movie', 'episode', 'short']); // نوع المحتوى
            $table->bigInteger('content_id'); // معرف المحتوى
            $table->integer('watch_duration_seconds')->default(0); // مدة المشاهدة بالثواني
            $table->decimal('completion_percentage', 5, 2)->default(0); // نسبة الإكمال
            $table->string('device_type', 50)->nullable(); // نوع الجهاز
            $table->string('quality_watched', 10)->nullable(); // الجودة المشاهدة
            $table->timestamp('watched_at')->useCurrent(); // تاريخ المشاهدة
            $table->timestamps(); // تواريخ الإنشاء والتحديث

            $table->index(['user_id', 'profile_id', 'watched_at']); // فهرس للبحث السريع
            $table->index(['content_type', 'content_id']); // فهرس للمحتوى
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('viewing_histories');
    }
};
