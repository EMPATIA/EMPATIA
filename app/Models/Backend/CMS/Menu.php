<?php

namespace App\Models\Backend\CMS;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Auditable;

class Menu extends Model
{
    use HasFactory, SoftDeletes, Auditable;

    protected $guarded = [];

    protected $casts = [
        'title' => 'object',
        'link' => 'object',
        'options' => 'object',
        'roles' => 'object',
        'versions' => 'object',
    ];

    protected static function newFactory()
    {
        return \Modules\CMS\Database\factories\MenuFactory::new();
    }

    public function parent()
    {
        return $this->belongsTo('App\Models\Backend\CMS\Menu','parent_id',  'id');
    }
}
