<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class TechnicalAnalysisQuestion extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'tech_analysis_question_key',
        'code',
        'cb_id',
        'acceptable',
        'created_by',
        'updated_by'
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
    protected $hidden = ['id','deleted_at'];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function technicalAnalysisQuestionTranslations(){
        return $this->hasMany('App\TechnicalAnalysisQuestionTranslation');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function cb(){
        return $this->belongsTo('App\Cb');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function technicalAnalysisQuestionAnswers(){
        return $this->hasMany('App\TechnicalAnalysisQuestionAnswer');
    }

    /**
     * @param null $language
     * @return bool
     */
    public function translation($language = null)
    {
        $translation = $this->hasMany('App\TechnicalAnalysisQuestionTranslation')->where('language_code', '=', $language)->get();
        if(sizeof($translation)>0){
            $this->setAttribute('question',$translation[0]->question);
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
        $translations = $this->hasMany('App\TechnicalAnalysisQuestionTranslation')->get()->keyBy('language_code');
        $this->setAttribute('translations',$translations);
        return $translations;
    }
}
