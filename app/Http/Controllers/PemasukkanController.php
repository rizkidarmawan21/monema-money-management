<?php

namespace App\Http\Controllers;

use App\Actions\Options\GetAkunSaldoOptions;
use App\Http\Requests\Pemasukan\CreatePemasukanRequest;
use App\Http\Resources\Pemasukan\PemasukanListResource;
use App\Http\Resources\SubmitDefaultResource;
use App\Models\Pemasukan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class PemasukkanController extends AdminBaseController
{
    public function indexPemasukkan()
    {
        $akun_saldo = (new GetAkunSaldoOptions)->handle();

        return Inertia::render('admin/transaksi/masuk/index', [
            'additional' => [
                'akun_saldo' => $akun_saldo,
            ]
        ]);
    }

    public function getData(Request $request)
    {
        try {
            $search = $request->search;
            $start_date = $request->start_date;
            $end_date = $request->end_date;

            $query = Pemasukan::query();

            $query->when(request('search'), function ($q) use ($search) {
                $q->where('nama_pemasukkan', 'LIKE', '%' . $search . '%');
            });

            $query->when(request('start_date', false) && request('end_date', false), function ($q) use ($start_date, $end_date) {
                $q->whereBetween('created_at', [$start_date, $end_date]);
            });

            $data = $query->paginate(10);

            $result =  new PemasukanListResource($data);

            return $this->respond($result);
        } catch (\Exception $e) {
            return $this->exceptionError($e->getMessage());
        }
    }

    public function createData(CreatePemasukanRequest $request)
    {
        try {
            DB::beginTransaction();
            $inputs = $request->only([
                'nama_pemasukkan',
                'nominal',
                'keterangan',
                'tanggal',
                'akun_saldo_id',
            ]);

            if (!isset($inputs['tanggal'])) {
                $inputs['tanggal'] = date('Y-m-d');
            }

            $inputs['user_id'] = auth()->user()->id;
            $inputs['tanggal'] = date('Y-m-d', strtotime($inputs['tanggal']));

            info($inputs);
            $pemasukan = Pemasukan::create($inputs);


            $pemasukan->transaksi()->create([
                'akun_saldo_id' => $pemasukan->akun_saldo_id,
                'jumlah' => $pemasukan->nominal,
                'keterangan' => $pemasukan->keterangan,
                'jenis_transaksi' => 'debet',
            ]);

            $pemasukan->akun_saldo->update([
                'saldo' => (int) $pemasukan->akun_saldo->saldo + (int) $pemasukan->nominal
            ]);
            $result = new SubmitDefaultResource($pemasukan, 'Pemasukan berhasil ditambahkan');

            DB::commit();
            return $this->respond($result);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->exceptionError($e->getMessage());
        }
    }

    public function updateData(CreatePemasukanRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            $inputs = $request->only([
                'nama_pemasukkan',
                'nominal',
                'keterangan',
                'tanggal',
                'akun_saldo_id',
            ]);

            if (!isset($inputs['tanggal'])) {
                $inputs['tanggal'] = date('Y-m-d');
            }

            $inputs['user_id'] = auth()->user()->id;
            $inputs['tanggal'] = date('Y-m-d', strtotime($inputs['tanggal']));
            $pemasukan = Pemasukan::findOrFail($id);

            $nominal_lama = $pemasukan->nominal;

            $pemasukan->update($inputs);

            $pemasukan->transaksi()->update([
                'akun_saldo_id' => $pemasukan->akun_saldo_id,
                'jumlah' => $pemasukan->nominal,
                'keterangan' => $pemasukan->keterangan,
            ]);

            $pemasukan->akun_saldo->update([
                'saldo' => (int) ($pemasukan->akun_saldo->saldo - $nominal_lama) + (int) $pemasukan->nominal
            ]);

            $result = new SubmitDefaultResource($pemasukan, 'Pemasukan berhasil diubah');

            DB::commit();
            return $this->respond($result);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->exceptionError($e->getMessage());
        }
    }

    public function deleteData($id)
    {
        try {
            DB::beginTransaction();
            $pemasukan = Pemasukan::findOrFail($id);

            $pemasukan->transaksi()->delete();

            $pemasukan->akun_saldo->update([
                'saldo' => (int) $pemasukan->akun_saldo->saldo - (int) $pemasukan->nominal
            ]);

            $pemasukan->delete();

            $result = new SubmitDefaultResource($pemasukan, 'Pemasukan berhasil dihapus');
            DB::commit();
            return $this->respond($result);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->exceptionError($e->getMessage());
        }
    }
}
