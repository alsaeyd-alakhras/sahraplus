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
        Schema::create('movie_cats', function (Blueprint $table) {
            $table->id(); // المعرف الفريد
            $table->foreignId('movie_id')->constrained('movies')->cascadeOnDelete(); // معرف الفيلم
            $table->foreignId('person_id')->constrained('people')->cascadeOnDelete(); // معرف الشخص
            $table->enum('role_type', ['actor', 'director', 'writer', 'producer', 'cinematographer', 'composer']); // نوع الدور
            $table->string('character_name', 100)->nullable(); // اسم الشخصية (للممثلين)
            $table->integer('sort_order')->default(0); // ترتيب الظهور
            $table->timestamps(); // تواريخ الإنشاء والتحديث
            $table->unique(['movie_id', 'person_id', 'role_type']); // منع تكرار نفس الشخص في نفس الدور
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movie_cats');
    }
};
