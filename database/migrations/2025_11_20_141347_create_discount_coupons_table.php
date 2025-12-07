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
        Schema::create('discount_coupons', function (Blueprint $table) {
            $table->id(); // المعرف الفريد
            $table->string('code', 50)->unique(); // كود القسيمة
            $table->string('name_ar', 100); // اسم القسيمة بالعربية
            $table->string('name_en', 100)->nullable(); // اسم القسيمة بالإنجليزية
            $table->text('description_ar')->nullable(); // وصف القسيمة بالعربية
            $table->text('description_en')->nullable(); // وصف القسيمة بالإنجليزية
            $table->enum('discount_type', ['percentage', 'fixed', 'free_trial']); // نوع الخصم
            $table->decimal('discount_value', 10, 2); // قيمة الخصم
            $table->decimal('min_amount', 10, 2)->default(0); // الحد الأدنى للمبلغ
            $table->decimal('max_discount', 10, 2)->nullable(); // الحد الأقصى للخصم
            $table->integer('usage_limit')->nullable(); // حد الاستخدام الإجمالي
            $table->integer('usage_limit_per_user')->default(1); // حد الاستخدام لكل مستخدم
            $table->integer('used_count')->default(0); // عدد مرات الاستخدام
            $table->timestamp('starts_at'); // تاريخ بداية الصلاحية
            $table->timestamp('expires_at'); // تاريخ انتهاء الصلاحية
            $table->json('applicable_plans')->nullable(); // الخطط المؤهلة للخصم
            $table->boolean('first_time_only')->default(false); // للمستخدمين الجدد فقط
            $table->boolean('is_active')->default(true); // حالة النشاط
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete(); // منشئ القسيمة
            $table->timestamps(); // تواريخ الإنشاء والتحديث
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discount_coupons');
    }
};
