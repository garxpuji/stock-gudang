<?php

namespace App\Http\Requests;

use App\Enums\DamageCategory;
use App\Enums\QcStatus;
use App\Enums\RecipientType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreReturnRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'returned_by' => 'required|string|max:255',
            'returned_by_contact' => 'nullable|string|max:100',
            'recipient_type' => ['required', Rule::enum(RecipientType::class)],
            'original_stock_out_id' => 'nullable|exists:stock_out_transactions,id',
            'reference_ticket_no' => 'nullable|string|max:100',
            'return_category' => 'required|string|max:100',
            'keterangan_pengembalian' => 'nullable|string',
            'transaction_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.qty' => 'required|numeric|min:0.01',
            'items.*.qc_status' => ['required', Rule::enum(QcStatus::class)],
            'items.*.serial_number' => 'nullable|string|max:100',
            'items.*.mac_address' => 'nullable|string|max:50',
            'items.*.qc_notes' => 'nullable|string',
            'items.*.damage_category' => ['nullable', Rule::enum(DamageCategory::class)],
            'items.*.damage_description' => 'nullable|string',
            'items.*.photo_proof' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'photo_proof' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ];
    }

    public function messages(): array
    {
        return [
            'returned_by.required' => 'Nama lengkap teknisi / karyawan yang menyerahkan fisik retur wajib diisi.',
            'return_category.required' => 'Kategori retur (Sisa Material / Dismantle / Pengembalian Alat) wajib dipilih.',
            'items.required' => 'Daftar barang kembali wajib diisi minimal 1 item.',
            'items.*.qc_status.required' => 'Status inspeksi QC (Baik / Rusak) wajib ditentukan untuk setiap item.',
        ];
    }
}
