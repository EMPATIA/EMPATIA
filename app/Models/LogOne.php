<?php

namespace App\Models;

use App\Http\Controllers\HelperController;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class LogOne extends Model
{

    protected $table = 'logs';

    protected $fillable = [
        'id',
        'date',
        'severity',
        'ip',
        'url',
        'method',
        'session_id',
        'user_agent',
        'user_id',
        'action',
        'result',
        'context',
        'details'
    ];

    protected $casts = [
        'details' => 'object'
    ];

    protected $dates = [
        'date'
    ];

    protected $appends = ['user_name'];

    public function getUserNameAttribute()
    {
        $user = $this->user()->first();
        return !empty($user) ? $user->name : null;
    }

    public function user() {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
