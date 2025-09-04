<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UserRatingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        $rating   = $this->route('user_rating'); // موجود عند التعديل
        $isUpdate = (bool) $rating;

        $contentType = $this->input('content_type', $rating?->content_type);

        $uniqueContentForUser = Rule::unique('user_ratings')
            ->ignore($rating?->id)
            ->where(fn($q) => $q
                ->where('user_id', auth()->id())
                ->where('content_type', $contentType)
            );

        return [
            'profile_id'   => ['nullable', 'exists:user_profiles,id'],

            'content_type' => array_filter([
                $isUpdate ? 'sometimes' : 'required',
                Rule::in(['movie','series','episode']),
            ]),

            'content_id'   => array_filter([
                $isUpdate ? 'sometimes' : 'required',
                'integer','min:1',
                $uniqueContentForUser,
            ]),

            'rating'       => [$isUpdate ? 'sometimes' : 'required', 'numeric','between:1,5'],
            'review'       => ['nullable','string'],
            'is_spoiler'   => [$isUpdate ? 'sometimes' : 'boolean'],
            'status'       => [$isUpdate ? 'sometimes' : 'nullable', Rule::in(['pending','approved','rejected'])],
        ];
    }

    public function prepareForValidation(): void
    {
        if ($this->has('is_spoiler')) {
            $this->merge([
                'is_spoiler' => filter_var($this->input('is_spoiler'), FILTER_VALIDATE_BOOL),
            ]);
        }
    }
   
   
}
