<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChatRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        if (! auth()->check()) {
            return false;
        }

        if ($this->has('chat_id') && $this->input('chat_id')) {
            $chatId = $this->input('chat_id');

            if (! is_numeric($chatId)) {
                $decodedId = \Vinkla\Hashids\Facades\Hashids::decode($chatId);
                $chatId = ! empty($decodedId) ? $decodedId[0] : $chatId;
            }

            $chat = \App\Models\Chat::find($chatId);

            return $chat && $chat->user_id === auth()->id();
        }

        return true;
    }

    /*
    |--------------------------------------------------------------------------
    | Validation Rules
    |--------------------------------------------------------------------------
    |
    | Define validation rules using configuration values for consistency.
    |
    */
    public function rules(): array
    {
        return [
            'prompt' => 'required|string|max:' . config('limits.validation.prompt_max_length'),
            'chat_id' => 'nullable|string',
            'model' => 'nullable|string|max:' . config('limits.validation.model_max_length'),
            'files.*' => 'nullable|file|max:' . config('limits.file_uploads.max_size') . '|mimes:' . implode(',', config('limits.file_uploads.allowed_mimes.all')),
        ];
    }
}
