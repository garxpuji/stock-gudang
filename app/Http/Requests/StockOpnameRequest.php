<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StockOpnameRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_id' => 'required|exists:products,id',
            'actual_stock' => 'required|numeric|min:0',
            'actual_damaged_stock' => 'nullable|numeric|min:0',
            'person_in_charge' => 'required|string|max:255',
            'notes' => 'required|string',
            'reference_doc_no' => 'nullable|string|max:100',
        ];
    }

    public function messages(): array
    {
        return [
            'product_id.required' => 'Pilih barang yang akan disesuaikan.',
            'actual_stock.required' => 'Hasil hitung stok fisik aktual wajib diisi.',
            'person_in_charge.required' => 'Nama auditor / pelaksana opname wajib diisi.',
            'notes.required' => 'Alasan / catatan penyesuaian opname wajib dicantumkan.',
        ];
    }
}
