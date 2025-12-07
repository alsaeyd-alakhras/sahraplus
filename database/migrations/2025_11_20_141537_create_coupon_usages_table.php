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
        Schema::create('coupon_usage', function (Blueprint $table) {
            $table->id(); // المعرف الفريد
            $table->foreignId('coupon_id')->constrained('discount_coupons')->cascadeOnDelete(); // معرف القسيمة
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete(); // معرف المستخدم
            $table->foreignId('subscription_id')->nullable()->constrained('user_subscriptions')->nullOnDelete(); // معرف الاشتراك
            $table->foreignId('payment_id')->nullable()->constrained('payments')->nullOnDelete(); // معرف الدفعة
            $table->decimal('original_amount', 10, 2); // المبلغ الأصلي
            $table->decimal('discount_amount', 10, 2); // مبلغ الخصم المطبق
            $table->decimal('final_amount', 10, 2); // المبلغ النهائي
            $table->string('currency', 3); // العملة
            $table->timestamp('used_at')->useCurrent(); // تاريخ الاستخدام
            $table->timestamps(); // تواريخ الإنشاء والتحديث

            $table->unique(['coupon_id', 'user_id', 'subscription_id']); // منع الاستخدام المتكرر
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupon_usages');
    }
};
