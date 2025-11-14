<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReviewStoreRequest extends FormRequest
{
    public function authorize(): true
    { return true; }

    public function rules(): array
    {
        return [
            'result' => 'required|in:easy,ok,hard',
            'comment' => 'nullable|string',
        ];
    }
}
