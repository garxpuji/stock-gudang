<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'sku' => 'required|string|max:50|unique:products,sku',
            'barcode' => 'nullable|string|max:100|unique:products,barcode',
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'unit_id' => 'required|exists:units,id',
            'location_id' => 'nullable|exists:locations,id',
            'stock' => 'nullable|numeric|min:0',
            'damaged_stock' => 'nullable|numeric|min:0',
            'min_stock' => 'required|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'has_serial_number' => 'nullable|boolean',
            'specification' => 'nullable|string',
            'stock_notes' => 'nullable|string',
        ];
    }
}
