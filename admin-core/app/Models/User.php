<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    // la tabla se llama usuarios, no users
    protected $table = 'usuarios';

    protected $fillable = ['nombre', 'email', 'password'];

    protected $hidden = ['password', 'remember_token'];

    public function productos()
    {
        return $this->hasMany(Producto::class, 'usuario_id');
    }
}
