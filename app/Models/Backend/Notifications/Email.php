<?php

namespace App\Models\Backend\Notifications;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use App\Traits\Auditable;


class Email extends Model
{
    use HasFactory, SoftDeletes, HasTimestamps, Auditable;

    protected $fillable = [
        'from_email',
        'from_name',
        'user_email',
        'user_id',
        'subject',
        'content',
        'data',
        'sent',
        'sent_at',
        'errors',
        'template',
        'message_id',
        'created_by',
    ];

    protected $casts = [
        'data' => 'object'
    ];

    protected static function newFactory()
    {
        return \Modules\Notifications\Database\factories\EmailFactory::new();
    }

    // public function owner(){
    //     return $this->belongsTo(User::class, 'user_id', 'id');
    // }
}
