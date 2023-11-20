<?php

namespace App\Http\Controllers;

use App\Http\Requests\Karyawan\CreateKaryawanRequest;
use App\Http\Resources\Karyawan\KaryawanListResource;
use App\Http\Resources\SubmitDefaultResource;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use Inertia\Inertia;

class KaryawanController extends AdminBaseController
{
    public function indexKaryawan()
    {
        return Inertia::render('admin/karyawan/index');
    }

    public function getData(Request $request)
    {
        try {
            $search = $request->search;

            $query = Karyawan::query();

            $query->when(request('search', false), function ($q) use ($search) {
                return $q->where('nama', 'like', '%' . $search . '%')
                    ->orWhere('telepon', 'like', '%' . $search . '%')
                    ->orWhere('alamat', 'like', '%' . $search . '%')
                    ->orWhere('jabatan', 'like', '%' . $search . '%')
                    ->orWhere('tempat_kerja', 'like', '%' . $search . '%')
                    ->orWhere('keterangan', 'like', '%' . $search . '%');
            });

            $data = $query->paginate(10);

            $result  = new KaryawanListResource($data);

            return $this->respond($result);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }

    public function createData(CreateKaryawanRequest $request)
    {
        try {
            $inputs = $request->only([
                'nama',
                'telepon',
                'alamat',
                'jabatan',
                'tempat_kerja',
                'keterangan',
            ]);

            $data = Karyawan::create($inputs);

            $result  = new SubmitDefaultResource($data, 'Karyawan berhasil ditambahkan');

            return $this->respond($result);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }

    public function updateData($id, CreateKaryawanRequest $request)
    {
        try {
            $inputs = $request->only([
                'nama',
                'telepon',
                'alamat',
                'jabatan',
                'tempat_kerja',
                'keterangan',
            ]);

            $data = Karyawan::findOrFail($id);

            $data->update($inputs);

            $result  = new SubmitDefaultResource($data, 'Karyawan berhasil diubah');

            return $this->respond($result);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }

    public function deleteData($id)
    {
        try {
            $data = Karyawan::findOrFail($id);

            $data->delete();

            $result  = new SubmitDefaultResource($data, 'Karyawan berhasil dihapus');

            return $this->respond($result);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }
}
