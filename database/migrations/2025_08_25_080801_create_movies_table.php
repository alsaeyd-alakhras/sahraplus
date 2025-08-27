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
        Schema::create('movies', function (Blueprint $table) {
            $table->id();
            $table->string('title_ar', 200); // العنوان بالعربية
            $table->string('title_en', 200)->nullable(); // العنوان بالإنجليزية
            $table->string('slug', 200)->unique(); // معرف URL
            $table->longText('description_ar')->nullable(); // الوصف بالعربية
            $table->longText('description_en')->nullable(); // الوصف بالإنجليزية
            $table->text('poster_url')->nullable(); // صورة البوستر
            $table->text('backdrop_url')->nullable(); // صورة الخلفية
            $table->text('trailer_url')->nullable(); // رابط التريلر
            $table->date('release_date')->nullable(); // تاريخ الإصدار
            $table->integer('duration_minutes')->nullable(); // المدة بالدقائق
            $table->decimal('imdb_rating', 3, 1)->nullable(); // تقييم IMDb
            $table->string('content_rating', 10)->nullable(); // التصنيف العمري
            $table->string('language', 5)->default('ar'); // لغة الفيلم
            $table->string('country', 2)->nullable(); // بلد الإنتاج
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft'); // حالة النشر
            $table->boolean('is_featured')->default(false); // مميز
            $table->bigInteger('view_count')->default(0); // عدد المشاهدات
            $table->string('tmdb_id', 20)->nullable(); // معرف TMDB
            $table->foreignId('created_by')->nullable()->constrained('admins')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movies');
    }
};
