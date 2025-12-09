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
        Schema::create('taxes', function (Blueprint $table) {
            $table->id(); // المعرف الفريد
            $table->string('name_ar', 100); // اسم الضريبة بالعربية
            $table->string('name_en', 100); // اسم الضريبة بالإنجليزية
            $table->string('tax_code', 20)->unique(); // رمز الضريبة
            $table->enum('tax_type', ['percentage', 'fixed']); // نوع الضريبة
            $table->decimal('tax_rate', 5, 3); // معدل الضريبة
            $table->json('applicable_countries')->nullable(); // الدول المطبقة عليها
            $table->json('applicable_plans')->nullable(); // الخطط المطبقة عليها
            $table->decimal('min_amount', 10, 2)->default(0); // الحد الأدنى للتطبيق
            $table->decimal('max_amount', 10, 2)->nullable(); // الحد الأقصى للتطبيق
            $table->boolean('compound_tax')->default(false); // ضريبة مركبة
            $table->integer('sort_order')->default(0); // ترتيب التطبيق
            $table->boolean('is_active')->default(true); // حالة النشاط
            $table->date('effective_from'); // تاريخ بداية التطبيق
            $table->date('effective_until')->nullable(); // تاريخ انتهاء التطبيق
            $table->timestamps(); // تواريخ الإنشاء والتحديث
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('taxes');
    }
};