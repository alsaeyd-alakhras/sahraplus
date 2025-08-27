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
        Schema::create('people', function (Blueprint $table) {
            $table->id(); // المعرف الفريد
            $table->string('name_ar', 100); // الاسم بالعربية
            $table->string('name_en', 100)->nullable(); // الاسم بالإنجليزية
            $table->text('bio_ar')->nullable(); // السيرة الذاتية بالعربية
            $table->text('bio_en')->nullable(); // السيرة الذاتية بالإنجليزية
            $table->text('photo_url')->nullable(); // صورة الشخص
            $table->date('birth_date')->nullable(); // تاريخ الميلاد
            $table->string('birth_place', 100)->nullable(); // مكان الولادة
            $table->string('nationality', 50)->nullable(); // الجنسية
            $table->enum('gender', ['male', 'female'])->nullable(); // الجنس
            $table->json('known_for')->nullable(); // مشهور بـ (array of professions)
            $table->string('tmdb_id', 20)->nullable(); // معرف TMDB
            $table->boolean('is_active')->default(true); // حالة النشاط
            $table->timestamps(); // تواريخ الإنشاء والتحديث
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('people');
    }
};
