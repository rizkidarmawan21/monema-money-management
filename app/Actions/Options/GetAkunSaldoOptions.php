<?php

namespace App\Actions\Options;

use App\Models\AkunSaldo;

class GetAkunSaldoOptions
{
    public function handle()
    {
        $akun_saldo = AkunSaldo::pluck('nama_akun', 'id');

        return $akun_saldo;
    }
}
