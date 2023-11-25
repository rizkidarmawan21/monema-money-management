<?php

namespace App\Http\Resources\Transaksi;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class HistoryTransactionListResource extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'data' => $this->transformCollection($this->collection),
            'meta' => [
                "success" => true,
                "message" => "Success get history transaction lists",
                'pagination' => $this->metaData()
            ]
        ];
    }

    private function transformData($data)
    {
        return [
            'id' => $data->id,
            'transaksi_id' => $data->transaksiable_id,
            'nama_transaksi' => $data->transaksiable_type,
            'nama_detail_transaksi' => $data->transaksiable->nama_pengeluaran ?? $data->transaksiable->nama_pemasukkan ?? null,
            'jenis_transaksi' => $data->jenis_transaksi,
            'jumlah' => (int) $data->jumlah,
            'keterangan' => $data->keterangan,
            'akun_saldo' => $data->akun_saldo->nama_akun,
            'karyawan' => $data->transaksiable->karyawan?->nama ?? null,
            'tanggal' => Carbon::parse($data->tanggal)->format('d F Y'),
            'creator' => $data->transaksiable->user->name ?? null,
        ];
    }

    private function transformCollection($collection)
    {
        return $collection->transform(function ($data) {
            return $this->transformData($data);
        });
    }

    private function metaData()
    {
        return [
            "total" => $this->total(),
            "count" => $this->count(),
            "per_page" => (int)$this->perPage(),
            "current_page" => $this->currentPage(),
            "total_pages" => $this->lastPage(),
            "links" => [
                "next" => $this->nextPageUrl()
            ],
        ];
    }
}
