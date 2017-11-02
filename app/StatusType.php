<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StatusType extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'status_type_key',
        'code',
        'position'
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
     * A Status Type has many Statuses.
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function status() {
        return $this->hasMany('App\Status');
    }

    /**
     * A Status Type has many Status Type Translations.
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function statusTypeTranslations() {
        return $this->hasMany('App\StatusTypeTranslation');
    }

    /**
     * @param null $language
     * @return bool
     */
    public function translation($language = null)
    {
        $translation = $this->hasMany('App\StatusTypeTranslation')->where('language_code', '=', $language)->get();
        if(sizeof($translation)>0){
            $this->setAttribute('name',$translation[0]->name);
            $this->setAttribute('description',$translation[0]->description);
            return true;
        } else {
            return false;
        }
    }

    public function translationByArray($values)
    {
        $this->setAttribute('name',$values['name']?? '');
        $this->setAttribute('description',$values['description']?? '');
    }

    /**
     * @return mixed
     */
    public function translations()
    {
        $translations = $this->hasMany('App\StatusTypeTranslation')->get();
        $this->setAttribute('translations',$translations);
        return $translations;
    }
}
