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
        Schema::create('watchlists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete(); // معرف المستخدم
            $table->foreignId('profile_id')->constrained('user_profiles')->cascadeOnDelete(); // معرف الملف الشخصي
            $table->enum('content_type', ['movie', 'series', 'episode']); // نوع المحتوى
            $table->bigInteger('content_id'); // معرف المحتوى
            $table->timestamp('added_at')->useCurrent(); // تاريخ الإضافة
            $table->timestamps(); // تواريخ الإنشاء والتحديث

            $table->unique(['profile_id', 'content_type', 'content_id']); // منع التكرار
            $table->index(['user_id', 'profile_id']); // فهرس للبحث السريع
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('watchlists');
    }
};
