<?php

namespace App\Http\Requests\Pemasukan;

use Illuminate\Foundation\Http\FormRequest;

class CreatePemasukanRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'nama_pemasukkan' => 'required|string',
            'nominal' => 'required|numeric',
            'keterangan' => 'nullable|string',
            'tanggal' => 'nullable|date',
            'akun_saldo_id' => 'required|exists:akun_saldos,id',
        ];
    }
}
