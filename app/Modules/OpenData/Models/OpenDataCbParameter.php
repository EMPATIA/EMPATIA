<?php

namespace App\Modules\OpenData\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OpenDataCbParameter extends Model
{

    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'created_by',
        'parameter_id',
        'open_data_entity_id'
    ];

    public function openData() {
        return $this->belongsTo('App\Modules\OpenData\Models\OpenData');
    }
    public function parameter() {
        return $this->belongsTo('App\Parameter');
    }
}
