<?php

namespace App\Http\Controllers;

use App\Http\Requests\Pengeluaran\CreatePengeluaranRequest;
use App\Http\Resources\Pengeluaran\PengeluaranListResource;
use App\Models\Pengeluaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PengeluaranController extends AdminBaseController
{
    public function indexPengeluaran()
    {
    }

    public function getData(Request $request)
    {
        try {
            $query = Pengeluaran::query();

            $data = $query->paginate(10);

            $result =  new PengeluaranListResource($data);

            return $this->respond($result);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }

    public function createData(CreatePengeluaranRequest $request)
    {
        try {
            DB::beginTransaction();
            $inputs  = $request->only([
                'nama_pengeluaran',
                'nominal',
                'keterangan',
                'tanggal',
                'karyawan_id',
                'akun_saldo_id',
            ]);

            if (is_null($inputs['tanggal'])) {
                $inputs['tanggal'] = date('Y-m-d');
            }

            $inputs['user_id'] = auth()->user()->id;

            $pengeluaran = Pengeluaran::create($inputs);

            $pengeluaran->transaksi()->create([
                'nominal' => $pengeluaran->nominal,
                'tanggal' => $pengeluaran->tanggal,
                'keterangan' => $pengeluaran->keterangan,
                'user_id' => $pengeluaran->user_id,
                'akun_saldo_id' => $pengeluaran->akun_saldo_id,
            ]);

            $pengeluaran->akun_saldo->update([
                'saldo' => (int) $pengeluaran->akun_saldo->saldo - (int) $pengeluaran->nominal
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->exceptionError($e->getMessage());
        }
    }
}
