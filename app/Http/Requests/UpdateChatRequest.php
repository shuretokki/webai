<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateChatRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $chat = $this->route('chat');

        return $this->user()
            ->can('update', $chat);
    }

    /*
    |--------------------------------------------------------------------------
    | Validation Rules
    |--------------------------------------------------------------------------
    */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:'.config('limits.validation.chat_title_max_length'),
        ];
    }
}
