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
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name_ar', 100); // اسم التصنيف بالعربية
            $table->string('name_en', 100); // اسم التصنيف بالإنجليزية
            $table->string('slug', 100)->unique(); // معرف URL
            $table->text('description_ar')->nullable(); // وصف بالعربية
            $table->text('description_en')->nullable(); // وصف بالإنجليزية
            $table->text('image_url')->nullable(); // صورة التصنيف
            $table->string('color', 7)->nullable(); // لون التصنيف (hex)
            $table->integer('sort_order')->default(0); // ترتيب العرض
            $table->boolean('is_active')->default(true); // حالة النشاط
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
