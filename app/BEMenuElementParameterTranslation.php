<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BEMenuElementParameterTranslation extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'language_code',
        'name',
        'description',
        'menu_item_parameter_id',
    ];

    protected $table = "be_menu_element_parameter_translations";

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['deleted_at'];

    /**
     * A BE Menu Element Parameter Translation belongs to one BE Menu Element Parameter
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parameter(){
        return $this->belongsTo('App\BEMenuElementParameter');
    }
}
