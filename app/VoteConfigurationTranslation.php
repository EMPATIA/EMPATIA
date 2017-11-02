<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VoteConfigurationTranslation extends Model
{
    use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['vote_configuration_id', 'language_code', 'name', 'description'];

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
     * A Vote Configuration Translation belongs to one Vote Configuration
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function voteConfiguration(){
        return $this->belongsTo('App\VoteConfiguration');
    }
}
