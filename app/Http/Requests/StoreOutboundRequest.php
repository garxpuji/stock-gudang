<?php

namespace App\Http\Requests;

use App\Enums\RecipientType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreOutboundRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'recipient_type' => ['required', Rule::enum(RecipientType::class)],
            'picked_up_by' => 'required|string|max:255',
            'recipient_department_or_team' => 'required|string|max:255',
            'recipient_phone' => 'nullable|string|max:50',
            'reference_ticket_no' => 'nullable|string|max:100',
            'purpose' => 'required|string|max:255',
            'keterangan_pengeluaran' => 'nullable|string',
            'transaction_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.qty' => 'required|numeric|min:0.01',
            'items.*.serial_ids' => 'nullable|array',
            'items.*.serial_ids.*' => 'exists:product_serials,id',
            'items.*.notes' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'picked_up_by.required' => 'Nama lengkap teknisi / karyawan yang mengambil barang fisik wajib diisi.',
            'recipient_department_or_team.required' => 'Nama tim lapangan / divisi penerima wajib diisi.',
            'purpose.required' => 'Tujuan pengeluaran barang (PSB, Maintenance, Kebutuhan NOC, dll) wajib diisi.',
            'items.required' => 'Minimal satu barang harus dipilih untuk dikeluarkan.',
            'items.*.product_id.required' => 'Produk wajib dipilih.',
            'items.*.qty.required' => 'Kuantitas barang keluar wajib diisi.',
        ];
    }
}
