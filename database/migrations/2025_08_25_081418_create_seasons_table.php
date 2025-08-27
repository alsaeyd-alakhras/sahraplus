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
        Schema::create('seasons', function (Blueprint $table) {
            $table->id(); // المعرف الفريد
            $table->foreignId('series_id')->constrained('series')->cascadeOnDelete(); // معرف المسلسل
            $table->integer('season_number'); // رقم الموسم
            $table->string('title_ar', 200)->nullable(); // عنوان الموسم بالعربية
            $table->string('title_en', 200)->nullable(); // عنوان الموسم بالإنجليزية
            $table->text('description_ar')->nullable(); // وصف الموسم بالعربية
            $table->text('description_en')->nullable(); // وصف الموسم بالإنجليزية
            $table->text('poster_url')->nullable(); // صورة الموسم
            $table->date('air_date')->nullable(); // تاريخ العرض
            $table->integer('episode_count')->default(0); // عدد الحلقات
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft'); // حالة النشر
            $table->string('tmdb_id', 20)->nullable(); // معرف TMDB للموسم
            $table->unique(['series_id', 'season_number']);
             // منع تكرار رقم الموسم لنفس المسلسل
            $table->timestamps(); // تواريخ الإنشاء والتحديث
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seasons');
    }
};
