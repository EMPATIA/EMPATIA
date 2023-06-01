<?php

namespace App\Models\Backend\CMS;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;

class Translation extends Model
{
    use HasFactory, SoftDeletes, HasTimestamps;

    protected $table = 'translations';

    protected $fillable = [
        'id',
        'locale',
        'namespace',
        'group',
        'item',
        'text',
    ];

    
}
