<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            // 1. Validasi Nama (Readonly di view, tapi tetap dikirim)
            'name' => ['required', 'string', 'max:255'],

            // 2. Validasi Email (Cek ke kolom email_work)
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
               // PERBAIKAN: Tambahkan parameter kedua 'employee_id'
                \Illuminate\Validation\Rule::unique(\App\Models\User::class, 'email_work')
                    ->ignore($this->user()->employee_id, 'employee_id'),
            ],

            // 3. Validasi Foto (DISINI TEMPATNYA)
            // jpg, jpeg, png, webp semua bisa masuk kategori 'image'
            'photo' => ['nullable', 'image', 'max:2048'], // Max 2MB
        ];
    }
}