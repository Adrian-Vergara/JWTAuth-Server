<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $table = 'usuarios';
    protected $primaryKey = 'id';
    protected  $fillable = array('id', 'nombre', 'apellido', 'email', 'password', 'fecha');
    public $timestamps = false;
}
