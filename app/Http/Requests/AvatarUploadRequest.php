<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AvatarUploadRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get validation rules for avatar upload
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $maxSize = config('limits.file_uploads.max_avatar_size');

        return [
            'avatar' => [
                'required',
                'image',
                'mimes:jpeg,jpg,png,gif,webp',
                "max:{$maxSize}",
                'dimensions:min_width=100,min_height=100,max_width=2000,max_height=2000',
            ],
        ];
    }

    /**
     * Custom error messages for avatar validation
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        $maxSizeMB = config('limits.file_uploads.max_avatar_size') / 1024;

        return [
            'avatar.required' => 'Please select an avatar image.',
            'avatar.image' => 'The avatar must be an image file.',
            'avatar.mimes' => 'The avatar must be a JPEG, PNG, GIF, or WebP image.',
            'avatar.max' => "The avatar must not exceed {$maxSizeMB}MB.",
            'avatar.dimensions' => 'The avatar must be between 100x100 and 2000x2000 pixels.',
        ];
    }
}
