<?php

namespace App\Models\Backend\CMS;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use App\Traits\Auditable;

class Language extends Model
{
    use HasFactory, SoftDeletes, HasTimestamps, Auditable;

    protected $table = 'languages';

    protected $fillable = [
        'id',
        'locale',
        'name',
        'default',
        'backend',
        'frontend',
    ];
}
