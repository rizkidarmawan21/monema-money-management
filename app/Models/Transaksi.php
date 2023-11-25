<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    use HasFactory;

    protected $guarded = [
        'id',
    ];

    public function transaksiable()
    {
        return $this->morphTo();
    }

    public function akun_saldo()
    {
        return $this->belongsTo(AkunSaldo::class);
    }
}
