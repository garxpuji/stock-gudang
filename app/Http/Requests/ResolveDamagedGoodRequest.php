<?php

namespace App\Http\Requests;

use App\Enums\DamagedActionTaken;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ResolveDamagedGoodRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'action_taken' => [
                'required',
                Rule::in([
                    DamagedActionTaken::RMA_VENDOR->value,
                    DamagedActionTaken::SCRAPPED->value,
                    DamagedActionTaken::REPAIRED_INTERNAL->value,
                ]),
            ],
            'action_notes' => 'nullable|string',
            'loss_cost' => 'nullable|numeric|min:0',
            'rma_number' => 'nullable|string|max:100',
        ];
    }

    public function messages(): array
    {
        return [
            'action_taken.required' => 'Pilihan tindakan lanjutan (RMA Vendor / Pemusnahan / Perbaikan) wajib dipilih.',
        ];
    }
}
