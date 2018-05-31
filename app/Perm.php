<?php

namespace App;

use Illuminate\Database\Eloquent\Model;



class Perm extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'code',
        'cb',
    ];

}