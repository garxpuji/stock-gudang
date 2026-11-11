<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $productId = $this->route('product') ? $this->route('product')->id : null;

        return [
            'sku' => ['required', 'string', 'max:50', Rule::unique('products', 'sku')->ignore($productId)],
            'barcode' => ['nullable', 'string', 'max:100', Rule::unique('products', 'barcode')->ignore($productId)],
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'unit_id' => 'required|exists:units,id',
            'location_id' => 'nullable|exists:locations,id',
            'min_stock' => 'required|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'has_serial_number' => 'nullable|boolean',
            'specification' => 'nullable|string',
            'stock_notes' => 'nullable|string',
        ];
    }
}
