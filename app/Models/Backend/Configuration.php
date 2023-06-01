<?php

namespace App\Models\Backend;

use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\Auditable;

class Configuration extends Model
{
    use Auditable;
    use HasFactory;
    use SoftDeletes;
    use HasTimestamps;

    protected $guarded = [];

    protected $casts = [
        'configurations' => 'object',
        'versions' => 'object'
    ];
}
