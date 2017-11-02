<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FlagTranslation extends Model
{
    use SoftDeletes;

    protected $fillable = ['flag_id','title','description','language_code'];
}
