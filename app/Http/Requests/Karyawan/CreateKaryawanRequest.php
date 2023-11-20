<?php

namespace App\Http\Requests\Karyawan;

use Illuminate\Foundation\Http\FormRequest;

class CreateKaryawanRequest extends FormRequest
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
            'nama' => 'required',
            'telepon' => 'required|unique:karyawans,telepon,' . $this->id . ',id,deleted_at,NULL',
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
            'nama.required' => 'Nama harus diisi',
            'telepon.required' => 'Telepon harus diisi',
            'telepon.unique' => 'Telepon sudah terdaftar',
        ];
    }
}
