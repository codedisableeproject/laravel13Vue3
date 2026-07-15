<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Penjualan extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'kode_penjualan',
        'tanggal_penjualan',
        'total',
        'status',
        'deleted',
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
        return $this->pembayarans()->sum('nilai_bayar');
    }
}
