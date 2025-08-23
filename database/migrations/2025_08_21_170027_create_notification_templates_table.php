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
        Schema::create('notification_templates', function (Blueprint $table) {
            $table->id(); // المعرف الفريد
            $table->string('name', 100)->unique(); // اسم القالب
            $table->string('subject_ar', 200); // موضوع الإشعار بالعربية
            $table->string('subject_en', 200)->nullable(); // موضوع الإشعار بالإنجليزية
            $table->longText('template_ar'); // قالب الرسالة بالعربية
            $table->longText('template_en')->nullable(); // قالب الرسالة بالإنجليزية
            $table->enum('type', ['email', 'push', 'sms', 'database']); // نوع الإشعار
            $table->json('variables')->nullable(); // المتغيرات المتاحة
            $table->boolean('is_active')->default(true); // حالة النشاط
            $table->timestamps(); // تواريخ الإنشاء والتحديث
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_templates');
    }
};
