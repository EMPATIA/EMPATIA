<?php

namespace App\Models\Empatia\Cbs;

use App\Traits\Auditable;
use App\Traits\SushiModel;
use App\Traits\WithBlamestamps;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TechnicalAnalysisQuestion extends Model
{
    use SushiModel, SoftDeletes, WithBlamestamps, Auditable;

    public $timestamps = true;
    protected $primaryKey = 'code';
    public $incrementing = false;
    protected $keyType = 'string';

    protected static Cb $cb;

    protected $schema = [
        'code'          => 'string',
        'enabled'       => 'boolean',
        'type'          => 'string',
        'value'         => 'json',
    ];

    protected $casts = [
        'code'          => 'string',
        'enabled'       => 'boolean',
        'type'          => 'string',
        'value'         => 'object',
    ];

    protected $fillable = [
        'code',
        'enabled',
        'type',
        'value'
    ];
}


