<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        // إزالة كلمة المرور من البيانات إذا كانت فارغة في التحديث
        if ($this->isUpdate() && empty($this->password)) {
            $this->getInputSource()->remove('password');
        }
    }

    public function rules(): array
    {
        return [
            'first_name' => 'required|string|max:100',
            'last_name' => 'nullable|string|max:100',
            'email' => [
                'required',
                'string',
                'email',
                Rule::unique('users', 'email')->ignore($this->route('user')?->id),
            ],
            'phone' => 'nullable|string|max:20',
            'password' => $this->isUpdate() ? 'sometimes|string|min:6' : 'required|string|min:6',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female',
            'country_code' => 'nullable|string|max:2',
            'language' => 'required|string|max:5',
            'avatar_url' => 'nullable|string',
            'is_active' => 'required|boolean',
            'is_banned' => 'required|boolean',
            'email_notifications' => 'required|boolean',
            'push_notifications' => 'required|boolean',
            'parental_controls' => 'required|boolean',
        ];
    }

    protected function isUpdate(): bool
    {
        return $this->isMethod('PUT') || $this->isMethod('PATCH') || $this->route('user');
    }
}
