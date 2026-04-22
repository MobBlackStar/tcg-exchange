<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreListingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Must be logged in to sell a card. (RoleMiddleware handles this generally, 
        // but it is good practice to explicitly require auth here).
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // 1. The Card ID must exist in our massive 14,000 card API database.
            // This prevents hackers from submitting fake card IDs.
            'card_id' => ['required', 'integer', 'exists:cards,id'],

            // 2. Condition must strictly be one of these strings. No weird inputs.
            'condition' => ['required', 'string', 'in:Mint,Near Mint,Lightly Played,Damaged'],

            // 3. Price must be a valid number, minimum 0.01 DT. No negative prices!
            'price' => ['required', 'numeric', 'min:0.01', 'max:99999.99'],

            // 4. Quantity must be an integer, at least 1, max 100.
            'quantity' => ['required', 'integer', 'min:1', 'max:100'],

            // 5. Image upload validation (Optional, max 2MB, must be an actual image file).
            // This natively prevents malicious .php or .exe file uploads!
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ];
    }

    /**
     * XSS PROTECTION HOOK
     * We intercept the data before validation to strip out any malicious HTML/JS tags
     * that a hacker might try to inject.
     */
    protected function prepareForValidation()
    {
        // We don't have text inputs in this specific form (like a description box), 
        // but if we did, we would strip tags here. Example:
        // $this->merge([
        //     'description' => strip_tags($this->description),
        // ]);
    }

    /**
     * Custom Error Messages (Optional, but looks very professional)
     */
    public function messages(): array
    {
        return [
            'card_id.exists' => 'The selected card does not exist in the global database.',
            'condition.in' => 'You must select a valid card condition.',
            'price.min' => 'You cannot sell a card for less than 0.01.',
            'photo.image' => 'The uploaded file must be a valid image format.',
            'photo.max' => 'The image size cannot exceed 2MB.',
        ];
    }
}