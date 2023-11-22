<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pengeluaran extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [
        'id',
    ];

    public function user()
    {
        return  $this->belongsTo(User::class);
    }

    public function transaksi()
    {
        return $this->morphTo(Transaksi::class, 'transaksiable');
    }

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class);
    }

    public function akun_saldo()
    {
        return $this->belongsTo(AkunSaldo::class);
    }
}
