<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PenjualanItem extends Model
{
    protected $fillable = [
        'penjualan_id',
        'item_id',
        'qty',
        'price',
        'total_price'
    ];

    public function penjualan()
    {
        return $this->belongsTo(Penjualan::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
