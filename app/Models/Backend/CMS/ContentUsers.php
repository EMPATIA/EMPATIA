<?php

namespace Modules\CMS\Entities;

use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\Auditable;
use App\Models\User;

class ContentUsers extends Model
{
    use HasFactory, SoftDeletes, HasTimestamps, Auditable;

    protected $guarded = [];

    protected $casts = [
        'title' => 'object',
    ];

    public function user() {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function content() {
        return $this->belongsTo(Content::class, 'content_id', 'id');
    }
}
