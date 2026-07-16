<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penjualan extends Model
{
    protected $fillable = [
        'kode_penjualan',
        'tanggal_penjualan',
        'total',
        'status',
        'deleted',
        'deleted_at',
        'deleted_by'
    ];

    protected $casts = [
        'tanggal_penjualan' => 'date',
    ];

    public function items()
    {
        return $this->hasMany(PenjualanItem::class);
    }

    public function pembayarans()
    {
        return $this->hasMany(Pembayaran::class);
    }

    public function totalSudahDibayar()
    {
        return $this->pembayarans()->where('deleted', 0)->sum('nilai_bayar');
    }
}
