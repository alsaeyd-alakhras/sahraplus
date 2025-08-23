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
        Schema::create('system_settings', function (Blueprint $table) {
            $table->id(); // المعرف الفريد
            $table->string('key', 100)->unique(); // مفتاح الإعداد
            $table->longText('value')->nullable(); // قيمة الإعداد
            $table->enum('type', ['string', 'number', 'boolean', 'json', 'text'])->default('string'); // نوع البيانات
            $table->string('group_name', 50)->default('general'); // مجموعة الإعدادات
            $table->string('label_ar', 200)->nullable(); // تسمية الإعداد بالعربية
            $table->string('label_en', 200)->nullable(); // تسمية الإعداد بالإنجليزية
            $table->text('description_ar')->nullable(); // وصف الإعداد بالعربية
            $table->text('description_en')->nullable(); // وصف الإعداد بالإنجليزية
            $table->boolean('is_public')->default(false); // متاح للجمهور عبر API
            $table->timestamps(); // تواريخ الإنشاء والتحديث
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_settings');
    }
};
