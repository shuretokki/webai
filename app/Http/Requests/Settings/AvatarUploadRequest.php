<?php

namespace App\Http\Requests\Settings;

use Illuminate\Foundation\Http\FormRequest;

class AvatarUploadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'avatar' => [
                'required',
                'image',
                'mimes:' . implode(',', config('limits.file_uploads.allowed_mimes.images')),
                'max:' . config('limits.file_uploads.avatar_max_size'),
            ],
        ];
    }

    public function messages(): array
    {
        $maxSize = config('limits.file_uploads.avatar_max_size');
        $allowedFormats = implode(', ', config('limits.file_uploads.allowed_mimes.images'));
        
        return [
            'avatar.required' => 'Please select an avatar image.',
            'avatar.image' => 'The file must be an image.',
            'avatar.mimes' => "The avatar must be a file of type: {$allowedFormats}.",
            'avatar.max' => "The image must not exceed {$maxSize}KB.",
        ];
    }
}
