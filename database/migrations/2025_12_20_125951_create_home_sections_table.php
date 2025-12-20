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
        Schema::create('home_sections', function (Blueprint $table) {
            $table->id();

            // أسماء القسم
            $table->string('title_ar', 150)->comment('عنوان القسم بالعربية');
            $table->string('title_en', 150)->nullable()->comment('عنوان القسم بالإنجليزية');

            // مكان العرض (API Mobile / Web)
            $table->enum('platform', ['mobile', 'web', 'both'])
                ->default('both')
                ->comment('مكان الظهور: mobile api / web / both');

            // أطفال
            $table->boolean('is_kids')->default(false)->comment('قسم مخصص للأطفال');

            // حالة التفعيل
            $table->boolean('is_active')->default(true)->comment('حالة القسم');

            // ترتيب الظهور في الهوم
            $table->integer('sort_order')->default(0)->comment('ترتيب القسم');

            // مدة العرض (اختياري)
            $table->timestamp('starts_at')->nullable()->comment('بداية العرض');
            $table->timestamp('ends_at')->nullable()->comment('نهاية العرض');

            $table->timestamps();

            // Indexes
            $table->index(['platform', 'is_active', 'is_kids']);
            $table->index(['starts_at', 'ends_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('home_sections');
    }
};
