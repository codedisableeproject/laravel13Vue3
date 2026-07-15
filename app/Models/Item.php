<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Item extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'kode',
        'nama',
        'image',
        'harga',
        'deleted',
        'deleted_by'
    ];

    public function penjualanItems()
    {
        return $this->hasMany(PenjualanItem::class);
    }
}
