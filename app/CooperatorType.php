<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CooperatorType extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'cooperator_type_key',
        'code'
    ];

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
     * Cooperator type has many cooperators
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function cooperators(){
        return $this->hasMany('App\Cooperator');
    }

    /**
     * Cooperator type has many cooperator type translations
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function cooperatorTypeTranslations(){
        return $this->hasMany('App\CooperatorTypeTranslation');
    }

    public function newTranslation($language = null, $languageDefault = null)
    {
        $translation = $this->hasMany('App\CooperatorTypeTranslation')->orderByRaw("FIELD(language_code,'".$languageDefault."','".$language."')DESC")->first();
        $this->setAttribute('name',$translation->name ?? null);
        $this->setAttribute('description',$translation->description ?? null);
    }
}
