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
        Schema::create('home_section_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('home_section_id')
                ->constrained('home_sections')
                ->cascadeOnDelete();

            // نوع المحتوى
            $table->enum('content_type', ['movie', 'series'])
                ->comment('نوع المحتوى');

            // ID الفيلم أو المسلسل
            $table->unsignedBigInteger('content_id')
                ->comment('معرف الفيلم أو المسلسل');

            // ترتيب العنصر داخل القسم
            $table->integer('sort_order')->default(0)->comment('ترتيب العنصر');

            $table->timestamps();

            // Indexes للأداء
            $table->index(['home_section_id', 'content_type']);
            $table->index(['content_type', 'content_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('home_section_items');
    }
};
