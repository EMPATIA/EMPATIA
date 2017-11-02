<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VoteConfiguration extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['vote_configuration_key', 'code'];

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
     * This defines a many-to-many relationship between Vote_Configurations and CB_Votes.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function cbVotes(){
        return $this->belongsToMany('App\CbVote', 'cb_vote_configurations')
            ->withPivot('value')
            ->withTimestamps();
    }

    /**
     * A Vote Configuration has many Vote COnfiguration Translations
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function voteConfigurationTranslations(){
        return $this->hasMany('App\VoteConfigurationTranslation');
    }

    /**
     * @param null $language
     * @return bool
     */
    public function translation($language = null)
    {
        $translation = $this->hasMany('App\VoteConfigurationTranslation')->where('language_code', '=', $language)->get();
        if(sizeof($translation)>0){
            $this->setAttribute('name',$translation[0]->name);
            $this->setAttribute('description',$translation[0]->description);
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return mixed
     */
    public function translations()
    {
        $translations = $this->hasMany('App\VoteConfigurationTranslation')->get();
        $this->setAttribute('translations',$translations);
        return $translations;
    }
}
