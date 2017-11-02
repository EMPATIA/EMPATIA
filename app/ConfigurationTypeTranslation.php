<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ConfigurationTypeTranslation extends Model
{
    use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['configuration_type_id', 'language_code', 'title', 'description'];

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
     * An Configuration Type Translation belongs to one Configuration Type
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function configurationType(){
        return $this->belongsTo('App\ConfigurationType');
    }
}
