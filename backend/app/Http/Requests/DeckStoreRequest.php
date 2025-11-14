<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DeckStoreRequest extends FormRequest
{
    public function authorize(): true
    { return true; }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ];
    }
}
