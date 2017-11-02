<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SiteAdditionalUrl extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'site_id',
        'link',
        'partial_link',
    ];
}
