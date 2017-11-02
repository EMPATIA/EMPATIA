<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BEMenuElementTranslation extends Model
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
        'be_menu_element_id',
    ];

    protected $table = "be_menu_element_translations";

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
     * A BE Menu Element Translation belongs to one BE Menu Element
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function configuration(){
        return $this->belongsTo('App\BEMenuElement');
    }
}
