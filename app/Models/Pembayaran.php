<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class Pembayaran extends Model
{
    protected $fillable = [
        'kode_pembayaran',
        'penjualan_id',
        'tanggal_pembayaran',
        'nilai_bayar',
        'deleted',
        'deleted_at',
        'deleted_by'
    ];

    protected $casts = [
        'tanggal_pembayaran' => 'date',
    ];

    public function penjualan()
    {
        return $this->belongsTo(Penjualan::class);
    }
}
