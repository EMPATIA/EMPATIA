<?php

namespace App\Models\Empatia;

use App\Traits\Auditable;
use App\Traits\Versionable;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LoginLevel extends Model
{
    use HasFactory, SoftDeletes, HasTimestamps, Auditable, Versionable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'empatia_login_levels';

    protected $guarded = [
        'id',
        'created_by',
        'created_at',
        'updated_by',
        'updated_at',
        'deleted_by',
        'deleted_at',
    ];


    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'name' => 'object',
        'dependencies' => 'object',
        'data' => 'object',
        'versions' => 'object'
    ];

}
