<?php

namespace App\Models\Backend;

use App\Models\User;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'original', 'type', 'size', 'public', 'created_by', 'updated_by', 'deleted_by',
    ];

    protected $casts = [
        'public' => 'boolean'
    ];
}
