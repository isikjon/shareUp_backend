<?php

namespace App\Modules\User\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'sometimes|string|max:255',
            'bio' => 'sometimes|string|max:1000',
            'avatar' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
    }
}

