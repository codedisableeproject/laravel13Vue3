<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $fillable = [
        'kode',
        'nama',
        'image',
        'harga',
        'deleted',
        'deleted_at',
        'deleted_by'
    ];

    public function penjualanItems()
    {
        return $this->hasMany(PenjualanItem::class);
    }
}
