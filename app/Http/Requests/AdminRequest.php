<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AdminRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function isUpdate(): bool
    {
        return $this->isMethod('PUT') || $this->isMethod('PATCH') || $this->route('admin');
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
        $adminId = $this->route('admin')?->id;
        return [
            'name' => 'required',
            'username' => [
                'nullable',
                'string',
                Rule::unique('admins', 'username')->ignore($adminId),
            ],
            'email' => [
                'required',
                'email',
                Rule::unique('admins', 'email')->ignore($adminId),
            ],
            'password' => $this->isUpdate() ? 'sometimes|string|min:6' : 'required|string|min:6',
            'confirm_password' => $this->isUpdate() ? 'nullable|string|min:6' : 'required|same:password',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'الاسم مطلوب',
            'username.unique' => 'اسم المستخدم موجود',
            'email.required' => 'البريد الالكتروني مطلوب',
            'email.unique' => 'البريد الالكتروني موجود',
            'password.required' => 'كلمة المرور مطلوبة',
            'confirm_password.required' => 'تاكيد كلمة المرور مطلوب',
            'confirm_password.same' => 'كلمة المرور غير متطابقة',
        ];
    }


}
