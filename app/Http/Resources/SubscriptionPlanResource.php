<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubscriptionPlanResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "id"                => $this->id,
            "name_ar"           => $this->name_ar,
            "name_en"           => $this->name_en,
            "slug"              => $this->slug,
            "description_ar"    => $this->description_ar,
            "description_en"    => $this->description_en,

            "price"             => $this->price,
            "currency"          => $this->currency,
            "billing_period"    => $this->billing_period, // monthly, yearly, weekly
            "trial_days"        => $this->trial_days,

            "max_profiles"      => $this->max_profiles,
            "max_devices"       => $this->max_devices,
            "video_quality"     => $this->video_quality, // sd, hd, uhd

            "download_enabled"  => $this->download_enabled,
            "ads_enabled"       => $this->ads_enabled,
            "live_tv_enabled"   => $this->live_tv_enabled,

            "features"          => $this->features,

            "sort_order"        => $this->sort_order,
            "is_popular"        => $this->is_popular,
            "is_active"         => $this->is_active,


            "created_at" => $this->created_at ? $this->created_at->format('Y-m-d') : null,
            "updated_at" => $this->updated_at ? $this->updated_at->format('Y-m-d') : null,

            // Relationship
            "limitations"       => PlanLimitationResource::collection($this->whenLoaded("limitations")),
            "countryPrices"       => $this->whenLoaded("countryPrices"),
            "contentAccess"       => $this->whenLoaded("contentAccess"),
        ];
    }
}
