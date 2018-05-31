<?php

namespace App;

use Illuminate\Database\Eloquent\Model;



class CbPermissionUser extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'cb_permission_code',
    ];

}