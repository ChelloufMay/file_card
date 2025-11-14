<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CardStoreRequest extends FormRequest
{
    public function authorize(): true
    { return true; }

    public function rules(): array
    {
        return [
            'deck_id' => 'required|exists:decks,id',
            'question' => 'required|string',
            'answer' => 'required|string',
            'tags' => 'nullable|array',
            'tags.*' => 'string',
        ];
    }
}
