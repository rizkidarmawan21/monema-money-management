<?php

namespace App\Http\Requests\Akun;

use Illuminate\Foundation\Http\FormRequest;

class CreateAkunRequest extends FormRequest
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
            'nama_akun' => 'required|unique:akun_saldos,nama_akun,' . $this->id . ',id,deleted_at,NULL',
            'keterangan' => 'nullable',
            'saldo' => 'required|integer|min:1',
        ];
    }


    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, mixed>
     */

    public function messages()
    {
        return [
            'nama_akun.required' => 'Nama akun harus diisi',
            'nama_akun.unique' => 'Nama akun sudah terdaftar',
            'saldo.required' => 'Saldo harus diisi',
            'saldo.integer' => 'Saldo harus berupa angka',
            'saldo.min' => 'Saldo minimal 1',
        ];
    }
}
