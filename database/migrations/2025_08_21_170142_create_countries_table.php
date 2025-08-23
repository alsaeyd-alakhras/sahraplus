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
        Schema::create('countries', function (Blueprint $table) {
            $table->id(); // المعرف الفريد
            $table->string('code', 2)->unique(); // رمز الدولة (SA, EG)
            $table->string('name_ar', 100); // اسم الدولة بالعربية
            $table->string('name_en', 100); // اسم الدولة بالإنجليزية
            $table->string('dial_code', 10); // رمز الاتصال
            $table->string('currency', 3); // رمز العملة
            $table->text('flag_url')->nullable(); // رابط علم الدولة
            $table->boolean('is_active')->default(true); // حالة النشاط
            $table->integer('sort_order')->default(0); // ترتيب العرض
            $table->timestamps(); // تواريخ الإنشاء والتحديث
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('countries');
    }
};
