<?php

namespace App\Http\Controllers;

use App\Http\Requests\Akun\CreateAkunRequest;
use App\Http\Resources\Akun\AkunSaldoListResource;
use App\Http\Resources\SubmitDefaultResource;
use App\Models\AkunSaldo;
use App\Models\Pemasukan;
use App\Models\Pengeluaran;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
            DB::beginTransaction();

            $data = AkunSaldo::findOrFail($id);

            // delete pengeluaran
            $dataPenge = Pengeluaran::where('akun_saldo_id', $id)->get();
            foreach ($dataPenge as $key => $value) {
                $value->delete();
            }

            // delete pemasukan
            $dataPemasukan = Pemasukan::where('akun_saldo_id', $id)->get();
            foreach ($dataPemasukan as $key => $value) {
                $value->delete();
            }

            // delete transaksi
            $dataTransaksi = Transaksi::where('akun_saldo_id', $id)->get();
            foreach ($dataTransaksi as $key => $value) {
                $value->delete();
            }

            $data->delete();

            DB::commit();

            $result  = new SubmitDefaultResource($data, 'Akun Saldo berhasil dihapus');

            return $this->respond($result);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->exceptionError($e->getMessage());
        }
    }
}
