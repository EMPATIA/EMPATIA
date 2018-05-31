<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cooperation extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['code'];

    /**
     * This defines a many-to-many relationship between Cooperations and Cooperators.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function cooperators() {
        return $this->belongsToMany('App\Cooperator');
    }
}
