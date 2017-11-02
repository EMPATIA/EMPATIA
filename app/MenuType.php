<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Menu
 * @package App
 */
class MenuType extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['type', 'module', 'title'];

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
     * Each MenuType belongs to a Menu.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function menu() {
        return $this->hasMany('App\Menu');
    }
}
