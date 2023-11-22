<?php

namespace App\Http\Resources\Pengeluaran;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Resources\Json\ResourceResponse;

class PengeluaranListResource extends ResourceCollection
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
                "message" => "Success get pengeluaran lists",
                'pagination' => $this->metaData()
            ]
        ];
    }

    private function transformData($data)
    {
        return [
            'id' => $data->id,
            'nama' => $data->nama_pengeluaran,
            'nominal' => $data->nominal,
            'keterangan' => $data->keterangan,
            'tanggal' => $data->tanggal,
            'karyawan' => $data->karyawan->nama,
            'akun_saldo' => $data->akun_saldo->nama_akun,
            'akun_saldo_id' => $data->akun_saldo_id,
            'user_id' => $data->user_id,
            'creator' => $data->user,
            'karyawan_id' => $data->karyawan_id,
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
