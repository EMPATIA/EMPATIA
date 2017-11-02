<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Permission extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['permission_key', 'code'];

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
    protected $hidden = ['id', 'deleted_at'];

    /**
     * This defines a many-to-many relationship between Permissions and Cooperators.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function cooperators() {
        return $this->belongsToMany('App\Cooperator');
    }
}
