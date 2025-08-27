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
        Schema::create('episods', function (Blueprint $table) {
            $table->id(); // المعرف الفريد
            $table->foreignId('season_id')->constrained('seasons')->cascadeOnDelete(); // معرف الموسم
            $table->integer('episode_number'); // رقم الحلقة في الموسم
            $table->string('title_ar', 200)->nullable(); // عنوان الحلقة بالعربية
            $table->string('title_en', 200)->nullable(); // عنوان الحلقة بالإنجليزية
            $table->text('description_ar')->nullable(); // وصف الحلقة بالعربية
            $table->text('description_en')->nullable(); // وصف الحلقة بالإنجليزية
            $table->text('thumbnail_url')->nullable(); // صورة مصغرة للحلقة
            $table->integer('duration_minutes')->nullable(); // مدة الحلقة بالدقائق
            $table->date('air_date')->nullable(); // تاريخ العرض
            $table->decimal('imdb_rating', 3, 1)->nullable(); // تقييم الحلقة
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft'); // حالة النشر
            $table->bigInteger('view_count')->default(0); // عدد المشاهدات
            $table->string('tmdb_id', 20)->nullable(); // معرف TMDB للحلقة
            $table->timestamps(); // تواريخ الإنشاء والتحديث
            $table->unique(['season_id', 'episode_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('episods');
    }
};
