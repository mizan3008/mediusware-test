<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EditFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title' => ['required', 'max:255'],
            'sku' => ['required', 'max:255', Rule::unique('products')->ignore($this->product->id)],
            'description' => ['nullable', 'max:1000'],
            'product_images' => ['nullable', 'array'],
            'variants.*' => ['nullable', 'array'],
            'product_variant_prices' => ['nullable', 'array'],
        ];
    }
}
