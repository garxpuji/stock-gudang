<?php

namespace App\Http\Requests;

use App\Enums\DamageCategory;
use App\Enums\RecipientType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDamagedGoodRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_id' => 'required|exists:products,id',
            'qty' => 'required|numeric|min:0.01',
            'reported_by' => 'required|string|max:255',
            'reporter_type' => ['required', Rule::enum(RecipientType::class)],
            'damage_category' => ['required', Rule::enum(DamageCategory::class)],
            'damage_description' => 'required|string',
            'serial_number' => 'nullable|string|max:100',
            'mac_address' => 'nullable|string|max:50',
            'photo_proof' => 'nullable|image|max:5120',
            'deduct_from_active_stock' => 'nullable|boolean',
            'reference_ticket_no' => 'nullable|string|max:100',
            'reported_at' => 'nullable|date',
        ];
    }

    public function messages(): array
    {
        return [
            'product_id.required' => 'Perangkat / material rusak wajib dipilih.',
            'reported_by.required' => 'Nama pelapor / teknisi yang menyerahkan wajib diisi.',
            'damage_category.required' => 'Kategori kerusakan teknis wajib dipilih.',
            'damage_description.required' => 'Keterangan detail kerusakan teknis wajib diisi.',
        ];
    }
}
