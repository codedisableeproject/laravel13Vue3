<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Role extends Model
{
    protected $table = 'roles';

    protected $fillable = [
        'name',
        'deleted',
        'deleted_at',
        'deleted_by',
    ];
    
    public function users()
    {
        return $this->hasMany(User::class, 'role_id');
    }
}