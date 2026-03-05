<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @property string $name
 * @property string $email
 * @property string $password
 * @property string $password_confirmation
 * @property string $role
 */
class StoreUserRequest extends FormRequest
{
    public function authorize()
    {
        // Only admins should be able to create users; middleware already ensures that
        return $this->user() && $this->user()->isAdmin();
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:' . implode(',', [
                \App\Models\User::ROLE_ADMIN,
                \App\Models\User::ROLE_USTADZ,
                \App\Models\User::ROLE_ORANG_TUA,
            ]),
        ];
    }
}
