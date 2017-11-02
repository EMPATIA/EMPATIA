<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TechnicalAnalysisQuestionAnswer extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'tec_a_q_ans_key',
        'technical_analysis_id',
        'technical_analysis_question_id',
        'value',
        'accepted',
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
    protected $hidden = ['deleted_at'];

    /**
     * Each Technical Analysis Questions Answers belongs to a Technical Analysis
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function technicalAnalysis()
    {
        return $this->belongsTo('App\TechnicalAnalysis');
    }

    /**
     * Each Technical Analysis Questions Answers belongs to a Technical Analysis Questions
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function technicalAnalysisQuestion()
    {
        return $this->belongsTo('App\TechnicalAnalysisQuestion');
    }
}
