<?php

namespace App\Models\Backend\Notifications;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Auditable;

class SMS extends Model
{
    use HasFactory, SoftDeletes, Auditable;

    protected $fillable = [
        'phone_number',
        'user_id',
        'message_id',
        'data',
        'content',
        'created_by',
        'sent',
        'sent_at',
        'template'
    ];

    protected $casts = [
        'data' => 'object'
    ];

    protected $table = 'sms';

    // public function owner(){
    //     return $this->belongsTo(User::class, 'user_id', 'id');
    // }


    protected static function newFactory()
    {
        return \Modules\Notifications\Database\factories\SMSFactory::new();
    }
}
