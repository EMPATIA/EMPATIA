<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Request;

class BEMenuElementParameter extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'key',
        'code'
    ];

    protected $table = "be_menu_element_parameters";

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

    /**
     * Each Menu Element Configuration has Many Menu Element Configuration Translations
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function translations() {
        return $this->hasMany('App\BEMenuElementParameterTranslation','be_menu_element_parameter_id');
    }

    public function newTranslation($language = null, $languageDefault = null)
    {
        $translation = $this->translations()->orderByRaw("FIELD(language_code,'".$languageDefault."','".$language."') DESC")->get();
        $this->setAttribute('name',$translation[0]->name);
        $this->setAttribute('description',$translation[0]->description);

        return array();
    }
}