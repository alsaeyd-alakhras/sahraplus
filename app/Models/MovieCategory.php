<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MovieCategory extends Model
{
    use HasFactory;

    // اسم الجدول صراحة (احتياطيًا)
    protected $table = 'movie_categories';

    /**
     * الحقول المسموح تعبئتها جماعيًا
     */
    protected $fillable = [
        'name_ar',
        'name_en',
        'slug',
        'description_ar',
        'description_en',
        'image_url',
        'color',
        'sort_order',
        'is_active',
    ];

    /**
     * تحويل الأنواع
     */
    protected $casts = [
        'is_active'  => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * علاقة Many-to-Many مع الأفلام عبر الجدول الوسيط category_movie_pivot
     */
    public function movies()
{
    return $this->belongsToMany(Movie::class, 'category_movie_pivot', 'category_id', 'movie_id');
}

    /**
     * سكوب للتصنيفات النشطة فقط
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * استخدام الـ slug بدل الـ id في ربط الروت (اختياري ومفيد)
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * إنشاء slug تلقائيًا إذا لم يتم تمريره
     */
    protected static function booted(): void
    {
        static::saving(function (MovieCategory $category) {
            if (empty($category->slug)) {
                // حاول من الإنجليزية، وإلا استخدم العربية
                $base = $category->name_en ?: $category->name_ar;
                // slug بسيط؛ في حالة العربية سيُحافظ على النص وقد تضيف/تخصص منطقًا آخر لاحقًا
                $slug = Str::slug($base, '-');

                // في حال كان الاسم عربي بالكامل وقد ينتج slug فارغ، استخدم نسخة مبسطة
                if ($slug === '' && !empty($base)) {
                    $slug = trim(preg_replace('/\s+/u', '-', $base), '-');
                }

                $category->slug = $slug;
            }
        });
    }
}
