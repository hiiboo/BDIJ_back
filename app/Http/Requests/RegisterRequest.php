<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            // 共通のルール
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'profile_image' => 'nullable',

            // guideのみのルール
            // 'level' => 'sometimes|integer|min:1|max:5',
            // 'introduction' => 'sometimes|string|max:1000',
            // 'hourly_rate' => 'sometimes|numeric|min:0',
            // 'birthday' => 'sometimes|date',
            // 'gender' => 'sometimes|in:male,female,other',
            'level' => 'sometimes',
            'introduction' => 'sometimes',
            'hourly_rate' => 'sometimes|numeric',
            'birthday' => 'sometimes|date',
            'gender' => 'sometimes',
        ];
    }

    public function getData()
    {
        $data = $this->validated();
        $data['password'] = bcrypt($data['password']);
        return $data;
    }
}
