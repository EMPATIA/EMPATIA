<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sms extends Model
{
    use SoftDeletes;

    protected $fillable = ['sms_key', 'user_id'];

    protected $dates = ['deleted_at'];

    protected $hidden = ['deleted_at', 'id'];

    public function user() {
        return $this->belongsTo('App\User');
    }
}
