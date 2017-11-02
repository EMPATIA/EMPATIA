<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Action extends Model
{
  use softDeletes;

  /**
  * The attributes that are mass assignable.
  *
  * @var array
  */
  protected $fillable = ['code', 'created_by'];

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
  

  public function cbQuestionnaires()
  {
    return $this->hasMany('App\CbQuestionnaries');
  }


  public function actionTranslations(){
    return $this->hasMany('App\ActionTranslation');
  }

  public function translation($language = null)
  {
      $translation = $this->hasMany('App\ActionTranslation')->where('language_code', '=', $language)->get();
      if(sizeof($translation)>0){
          $this->setAttribute('title',$translation[0]->title);
          $this->setAttribute('description',$translation[0]->description);
          return true;
      } else {
          return false;
      }
  }


}
