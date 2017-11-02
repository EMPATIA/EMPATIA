<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CbQuestionnaries extends Model
{
    use SoftDeletes;


    /**
    * The attributes that are mass assignable.
    *
    * @var array
    */
    protected $fillable = ['cb_questionnarie_key', 'cb_id', 'questionnarie_key', 'action_id', 'notify_email', 'ignore', 'days_to_ignore'];

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

    public function cbQuestionnaireTranslation(){
      return $this->hasMany('App\CbQuestionnarieTranslation', 'cb_questionnarie_id');
    }

    public function action(){
        return $this->belongsTo('App\Action');
    }

    public function cb(){
        return $this->belongsTo('App\Cb');
    }

    public function cbQuestionnairesUser(){
        return $this->belongsToMany('App\OrchUser', 'cb_questionnaries_user', 'cb_questionnarie_id','user_id')
            ->withPivot('date_ignore')
            ->withTimestamps();
    }

    public function cbQuestionnaireVote(){
        return $this->hasOne('App\CbQuestionnaireVote', 'cb_questionnaire_id');
    }

}
