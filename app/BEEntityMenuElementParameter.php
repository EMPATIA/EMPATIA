<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Request;

class BEEntityMenuElementParameter extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'value',
        'be_entity_menu_element_id',
        'be_menu_element_parameter_id'
    ];

    protected $table = "be_entity_menu_element_parameters";

    /**
     * The attributes that should be mutated to dates.
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * The attributes that should be hidden for arrays.
     * @var array
     */
    protected $hidden = ['deleted_at'];

    
    public function element() {
        return $this->belongsTo('App\BEEEntityMenuElement');
    }

    public function parameter() {
        return $this->belongsTo('App\BEMenuElementParameter','be_menu_element_parameter_id','id');
    }
}