<?php

namespace App\Http\Requests\Pengeluaran;

use Illuminate\Foundation\Http\FormRequest;

class CreatePengeluaranRequest extends FormRequest
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
            'nama_pengeluaran' => 'required|string',
            'nominal' => 'required|numeric',
            'keterangan' => 'nullable|string',
            'tanggal' => 'nullable|date',
            'karyawan_id' => 'nullable|exists:karyawans,id',
            'akun_saldo_id' => 'required|exists:akun_saldos,id',
        ];
    }
}
