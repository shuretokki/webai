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
            $chat = \App\Models\Chat::find($this->input('chat_id'));

            return $chat && $chat->user_id === auth()->id();
        }

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
            'prompt' => 'required|string|max:10000',
            'chat_id' => 'nullable|exists:chats,id',
            'model' => 'nullable|string|max:100',
            'files.*' => 'nullable|file|max:10240|mimes:jpeg,jpg,png,gif,pdf,txt,doc,docx',
        ];
    }
}
