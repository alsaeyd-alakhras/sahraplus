<?php

namespace App\Actions\Fortify;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input)
    {
        if(Config::get('fortify.guard') == 'admins'){
            Validator::make($input, [
                'username' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique(Admin::class),
                ],
            ])->validate();
            if(isset($input['avatar']) && $input['avatar'] != null){
                $avatar = $input['avatar'];
                $avatar = $avatar->store('avatars');
            }else{
                $avatar = null;
            }
            return Admin::create([
                'name' => $input['name'],
                'username' => $input['username'],
                'password' => Hash::make($input['password']),
                'email' => $input['email'],
                'super_admin' => false,
                'is_active' => $input['is_active'] ?? true,
                'last_activity' => now(),
                'avatar' => $avatar,
            ]);
        }
        Validator::make($input, [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['nullable', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique(User::class),
            ],
            'password' => $this->passwordRules(),
        ])->validate();

        return User::create([
            'first_name' => $input['first_name'],
            'last_name' => $input['last_name'],
            'email' => $input['email'],
            'phone' => $input['phone'] ?? null,
            'password' => Hash::make($input['password']),
            'date_of_birth' => $input['date_of_birth'] ?? null,
            'gender' => $input['gender'] ?? null,
            'country_code' => $input['country_code'] ?? null,
            'language' => $input['language'] ?? null,
            'avatar_url' => $input['avatar_url'] ?? null,
            'is_active' => $input['is_active'] ?? true,
            'is_banned' => $input['is_banned'] ?? false,
            'email_notifications' => $input['email_notifications'] ?? true,
            'push_notifications' => $input['push_notifications'] ?? true,
            'parental_controls' => $input['parental_controls'] ?? false,
            'last_activity' => now(),
        ]);
    }
}
