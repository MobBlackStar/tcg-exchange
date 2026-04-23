<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            // The rating MUST be between 1 and 5.
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            
            // The comment is optional, but if they write one, limit it to 500 chars to prevent spam.
            'comment' => ['nullable', 'string', 'max:500'],
            
            // Ensure they are reviewing a real order that belongs to them.
            'order_id' => ['required', 'integer', 'exists:orders,id'],
        ];
    }
}