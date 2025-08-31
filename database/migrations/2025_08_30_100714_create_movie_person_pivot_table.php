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
        Schema::create('movie_person_pivot', function (Blueprint $table) {
            $table->id();
            $table->foreignId('movie_id')
              ->constrained('movies')
              ->cascadeOnDelete();
            $table->foreignId('person_id')
              ->constrained('people')
              ->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movie_person_pivot');
    }
};
