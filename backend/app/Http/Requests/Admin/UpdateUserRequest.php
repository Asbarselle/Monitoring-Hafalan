<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @property string $name
 * @property string $email
 * @property string|null $password
 * @property string|null $password_confirmation
 * @property string $role
 */
class UpdateUserRequest extends FormRequest
{
    public function authorize()
    {
        return $this->user() && $this->user()->isAdmin();
    }

    public function rules()
    {
        // get the user id from route parameter
        $id = $this->route('id');
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|in:' . implode(',', [
                \App\Models\User::ROLE_ADMIN,
                \App\Models\User::ROLE_USTADZ,
                \App\Models\User::ROLE_ORANG_TUA,
            ]),
        ];
    }
}
