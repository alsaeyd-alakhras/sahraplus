<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HomeBannerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $content = $this->content;
        
        return [
            'id' => $this->id,
            'placement' => $this->placement,
            'is_kids' => $this->is_kids,
            'sort_order' => $this->sort_order,
            'content_type' => $this->content_type,
            'content_id' => $this->content_id,
            
            // بيانات المحتوى
            'title' => $content ? (app()->getLocale() === 'ar' 
                ? ($content->title_ar ?? $content->title_en ?? 'N/A')
                : ($content->title_en ?? $content->title_ar ?? 'N/A')) : 'N/A',
            'slug' => $content->slug ?? null,
            'poster' => $content->poster_full_url ?? null,
            'backdrop' => $content->backdrop_full_url ?? null,
            'description' => $content ? (app()->getLocale() === 'ar'
                ? ($content->description_ar ?? $content->description_en ?? '')
                : ($content->description_en ?? $content->description_ar ?? '')) : '',
            
            // معلومات إضافية
            'type' => $this->content_type,
            'url' => $this->getContentUrl(),
        ];
    }

    /**
     * Get the URL for the content
     */
    private function getContentUrl(): ?string
    {
        $content = $this->content;
        if (!$content) return null;

        if ($this->content_type === 'movie') {
            return route('site.movie.show', $content->slug);
        } elseif ($this->content_type === 'series') {
            return route('site.series.show', $content->slug);
        }

        return null;
    }
}

