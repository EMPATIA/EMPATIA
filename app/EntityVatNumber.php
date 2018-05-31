<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EntityVatNumber extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'entity_id',
        'parameter_user_type_id',
        'vat_number',
        'name',
        'surname',
        'birthdate',
        'birthplace',
        'residential_address',
        'gender'
    ];
}
