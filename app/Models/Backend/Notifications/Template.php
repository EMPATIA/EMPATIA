<?php

namespace App\Models\Backend\Notifications;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Auditable;


class Template extends Model
{
    use HasFactory, SoftDeletes, Auditable;

    protected $guarded = [
        'id',
        'created_by',
        'created_at',
        'updated_by',
        'updated_at',
        'deleted_by',
        'deleted_at',
    ];

    protected $casts = [
        'subject' => 'object',
        'content' => 'object',
        'versions' => 'object',
        'data' => 'object',
    ];

    protected static function newFactory()
    {
        return \Modules\Notifications\Database\factories\TemplateFactory::new();
    }
}
