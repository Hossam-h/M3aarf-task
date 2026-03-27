<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * FetchCategoriesRequest
 *
 * Custom form request for validating the categories input
 * when the user submits the fetch form.
 */
class FetchCategoriesRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'categories' => ['required', 'string', 'min:2', 'max:5000'],
        ];
    }

    /**
     * Get custom validation messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'categories.required' => 'Please enter at least one category. | يرجى إدخال فئة واحدة على الأقل.',
            'categories.string'   => 'Categories must be text. | يجب أن تكون الفئات نصًا.',
            'categories.min'      => 'Category text must be at least 2 characters. | يجب أن يكون النص حرفين على الأقل.',
            'categories.max'      => 'Category text is too long (max 5000 characters). | النص طويل جدًا.',
        ];
    }

    /**
     * Parse the categories textarea into a clean array.
     *
     * Splits by newline, trims whitespace, and removes empty lines.
     *
     * @return array<int, string>
     */
    public function getCategories(): array
    {
        $raw = $this->input('categories', '');

        return array_values(
            array_filter(
                array_map('trim', preg_split('/\r\n|\r|\n/', $raw)),
                fn($line) => !empty($line)
            )
        );
    }
}
