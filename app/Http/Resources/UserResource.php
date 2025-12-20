<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'full_name' => $this->full_name,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'pin_code' => $this->pin_code,
            'phone' => $this->phone,
            'date_of_birth' => $this->date_of_birth,
            'gender' => $this->gender,
            'country_code' => $this->country_code,
            'language' => $this->language,
            'avatar_url' => $this->avatar_full_url,
            'is_active' => $this->is_active,
            'is_banned' => $this->is_banned,
            'email_notifications' => $this->email_notifications,
            'push_notifications' => $this->push_notifications,
            'parental_controls' => $this->parental_controls,
            'last_activity' => $this->last_activity,
            'profiles' => UserProfileResource::collection($this->profiles),
            'sessions' => UserSessionResource::collection($this->sessions),
            'notifications' => NotificationResource::collection($this->notifications),
            'country' => $this->country ? new CountryResource($this->country) : null,
        ];
    }
}
