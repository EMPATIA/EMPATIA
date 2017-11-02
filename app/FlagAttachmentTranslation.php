<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FlagAttachmentTranslation extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'relation_id',
        'relation_type_code',
        'language_code',
        'description',
        'version',
        'active',
    ];
}
