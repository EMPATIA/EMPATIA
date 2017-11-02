<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TechnicalAnalysis extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'technical_analysis_key',
        'topic_id',
        'impact',
        'budget',
        'execution',
        'sustainability',
        'decision',
        'created_by',
        'updated_by',
        'active',
        'version'
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
     * Each Technical Analysis belongs to a Topic
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function topic()
    {
        return $this->belongsTo('App\Topic');
    }

    /**
     * Each Technical Analysis has many Technical Analysis Questions Answers
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function technicalAnalysisQuestionsAnswers()
    {
        return $this->hasMany('App\TechnicalAnalysisQuestionAnswer');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function technicalAnalysisQuestions()
    {
        return $this->hasManyThrough('App\TechnicalAnalysisQuestion', 'App\TechnicalAnalysisQuestionAnswer', 'technical_analysis_id', 'id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function technicalAnalysisNotifications()
    {
        return $this->hasMany('App\TechnicalAnalysisNotification');
    }

}
