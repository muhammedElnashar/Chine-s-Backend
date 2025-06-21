<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class UpdateAdminRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' .  $this->route('user')->id,
            'password' => ['nullable', Password::min(8)->mixedCase()->numbers()->symbols()],
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'role' => 'required|in:admin,super_admin',
        ];
    }
}
