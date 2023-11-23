<?php 

namespace App\Actions\Options;

use App\Models\Karyawan;

class GetKaryawanOptions
{
    public function handle()
    {
        $karyawan = Karyawan::pluck('nama', 'id');

        return $karyawan;
    }
}