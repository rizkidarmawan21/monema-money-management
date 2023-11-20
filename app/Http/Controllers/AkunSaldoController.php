<?php

namespace App\Http\Controllers;

use App\Http\Requests\Akun\CreateAkunRequest;
use App\Http\Resources\Akun\AkunSaldoListResource;
use App\Http\Resources\SubmitDefaultResource;
use App\Models\AkunSaldo;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AkunSaldoController extends AdminBaseController
{
    public function indexAkun()
    {
        return Inertia::render('admin/akun/index');
    }

    public function getData(Request $request)
    {
        try {
            $search  = $request->search;

            $query = AkunSaldo::query();

            $query->when(request('search', false), function ($q) use ($search) {
                return $q->where('nama_akun', 'like', '%' . $search . '%');
            });

            $data = $query->paginate(10);

            $result  = new AkunSaldoListResource($data);

            return $this->respond($result);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }

    public function createData(CreateAkunRequest $request)
    {
        try {
            $inputs = $request->only([
                'nama_akun',
                'keterangan',
                'saldo',
            ]);

            $data = AkunSaldo::create($inputs);

            $result  = new SubmitDefaultResource($data, 'Akun Saldo berhasil ditambahkan');

            return $this->respond($result);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }

    public function updateData(CreateAkunRequest $request, $id)
    {
        try {
            $inputs = $request->only([
                'nama_akun',
                'keterangan',
                'saldo',
            ]);

            $data = AkunSaldo::findOrFail($id);

            $data->update($inputs);

            $result  = new SubmitDefaultResource($data, 'Akun Saldo berhasil diubah');

            return $this->respond($result);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }

    public function deleteData($id)
    {
        try {
            $data = AkunSaldo::findOrFail($id);

            $data->delete();

            $result  = new SubmitDefaultResource($data, 'Akun Saldo berhasil dihapus');

            return $this->respond($result);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }
}
