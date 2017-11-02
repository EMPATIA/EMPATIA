<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FlagTypeTranslation extends Model
{
    use SoftDeletes;

    protected $fillable = ['flag_type_id','title','description','language_code'];
}
