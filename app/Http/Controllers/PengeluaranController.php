<?php

namespace App\Http\Controllers;

use App\Actions\Options\GetAkunSaldo;
use App\Actions\Options\GetAkunSaldoOptions;
use App\Actions\Options\GetKaryawanOptions;
use App\Http\Requests\Pengeluaran\CreatePengeluaranRequest;
use App\Http\Resources\Pengeluaran\PengeluaranListResource;
use App\Http\Resources\SubmitDefaultResource;
use App\Models\Pengeluaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class PengeluaranController extends AdminBaseController
{
    public function indexPengeluaran()
    {
        $akun_saldo = (new GetAkunSaldoOptions)->handle();
        $karyawans = (new GetKaryawanOptions)->handle();

        return Inertia::render('admin/transaksi/keluar/index', [
            'additional' => [
                'akun_saldo' => $akun_saldo,
                'karyawans' => $karyawans
            ]
        ]);
    }

    public function getData(Request $request)
    {
        try {
            $search = $request->search;
            $start_date = $request->start_date;
            $end_date = $request->end_date;

            $query = Pengeluaran::query();

            $query->when(request('search'), function ($q) use ($search) {
                $q->where('nama_pengeluaran', 'LIKE', '%' . $search . '%');
            });

            $query->when(request('start_date', false) && request('end_date', false), function ($q) use ($start_date, $end_date) {
                $q->whereBetween('created_at', [$start_date, $end_date]);
            });

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

            if (!isset($inputs['tanggal'])) {
                $inputs['tanggal'] = date('Y-m-d');
            }

            $inputs['user_id'] = auth()->user()->id;
            $inputs['tanggal'] = date('Y-m-d', strtotime($inputs['tanggal']));

            $pengeluaran = Pengeluaran::create($inputs);

            $pengeluaran->transaksi()->create([
                'akun_saldo_id' => $pengeluaran->akun_saldo_id,
                'jumlah' => $pengeluaran->nominal,
                'keterangan' => $pengeluaran->keterangan,
                'jenis_transaksi' => 'kredit',
            ]);

            $pengeluaran->akun_saldo->update([
                'saldo' => (int) $pengeluaran->akun_saldo->saldo - (int) $pengeluaran->nominal
            ]);

            DB::commit();
            $result = new SubmitDefaultResource($pengeluaran, 'Pengeluaran berhasil ditambahkan');
            return $this->respond($result);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->exceptionError($e->getMessage());
        }
    }

    public function updateData(CreatePengeluaranRequest $request, $id)
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

            $pengeluaran = Pengeluaran::findOrFail($id);

            $nominal_lama = $pengeluaran->nominal;

            $pengeluaran->update($inputs);

            $pengeluaran->transaksi()->update([
                'nominal' => $pengeluaran->nominal,
                'tanggal' => $pengeluaran->tanggal,
                'keterangan' => $pengeluaran->keterangan,
                'user_id' => $pengeluaran->user_id,
                'akun_saldo_id' => $pengeluaran->akun_saldo_id,
            ]);

            $pengeluaran->akun_saldo->update([
                'saldo' => (int) ($pengeluaran->akun_saldo->saldo + $nominal_lama) - (int) $pengeluaran->nominal
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->exceptionError($e->getMessage());
        }
    }

    public function deleteData($id)
    {
        try {
            DB::beginTransaction();

            $pengeluaran = Pengeluaran::findOrFail($id);

            $pengeluaran->transaksi()->delete();

            $pengeluaran->delete();

            $pengeluaran->akun_saldo->update([
                'saldo' => (int) $pengeluaran->akun_saldo->saldo + (int) $pengeluaran->nominal
            ]);

            $result = new SubmitDefaultResource($pengeluaran, 'Pengeluaran berhasil dihapus');

            DB::commit();
            return $this->respond($result);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->exceptionError($e->getMessage());
        }
    }
}
