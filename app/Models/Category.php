<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory;

    // اسم الجدول صراحة (احتياطيًا)
    protected $table = 'categories';

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

    protected $appends = ['is_favorite'];

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
        return $this->belongsToMany(Movie::class, 'category_movie_pivot', 'category_id', 'movie_id')
            ->withTimestamps();
    }

    public function series()
    {
        return $this->belongsToMany(Series::class, 'category_series_pivot', 'category_id', 'series_id')
            ->withTimestamps();
    }

    public function getIsFavoriteAttribute()
    {
        return Favorite::where([
            'content_type' => 'movie',
            'content_id' => $this->id,
        ])->exists();
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
        static::saving(function (Category $category) {
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

    // Accessor
    public function getImageFullUrlAttribute()
    {
        // إذا كان الرابط http/https تأكد أنه يعمل قبل الإرجاع
        if (Str::startsWith($this->image_url, ['http', 'https'])) {
            try {
                $headers = @get_headers($this->image_url);
                if ($headers && strpos($headers[0], '200') !== false) {
                    return $this->image_url;
                }
            } catch (\Exception $e) {
                // Nothing, will fallback to default
                return asset('assets-site/images/categories/1.png');
            }
        }

        if ($this->image_url) {
            return asset('storage/' . $this->image_url);
        }
        return asset('assets-site/images/categories/1.png');
    }
}
