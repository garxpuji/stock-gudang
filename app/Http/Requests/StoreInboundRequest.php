<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreInboundRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'supplier_id' => 'nullable|exists:suppliers,id',
            'supplier_name' => 'nullable|string|max:255',
            'po_number' => 'nullable|string|max:100',
            'vendor_delivery_order_no' => 'nullable|string|max:100',
            'courier_or_driver_name' => 'nullable|string|max:255',
            'transaction_date' => 'required|date',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.qty' => 'required|numeric|min:0.01',
            'items.*.cost_price' => 'nullable|numeric|min:0',
            'items.*.serial_numbers' => 'nullable|string',
            'items.*.notes' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'items.required' => 'Daftar item barang masuk wajib diisi minimal 1 item.',
            'items.*.product_id.required' => 'Produk wajib dipilih.',
            'items.*.qty.required' => 'Kuantitas masuk wajib diisi dan lebih besar dari 0.',
        ];
    }
}
