<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pemasukan extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [
        'id',
    ];

    public function transaksi()
    {
        return $this->morphOne(Transaksi::class, 'transaksiable');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function akun_saldo()
    {
        return $this->belongsTo(AkunSaldo::class);
    }
}
