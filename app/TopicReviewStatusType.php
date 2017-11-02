<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TopicReviewStatusType extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'topic_review_status_type_key',
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
    protected $hidden = ['id','deleted_at'];



    /**
     * A TopicReview Status Type has many TopicReview Statuses.
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function topicReviewStatus() {
        return $this->hasMany('App\TopicReviewStatus');
    }

    /**
     * A TopicReview Status Type has many TopicReview Status Type Translations.
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function TopicReviewStatusTypeTranslations() {
        return $this->hasMany('App\TopicReviewStatusTypeTranslation');
    }


    /**
     * @return mixed
     */
    public function translations()
    {
        $translations = $this->hasMany('App\TopicReviewStatusTypeTranslation')->get();
        $this->setAttribute('translations',$translations);
        return $translations;
    }


    /**
     * @param null $language
     * @return bool
     */
    public function translation($language = null)
    {
        $translation = $this->hasMany('App\TopicReviewStatusTypeTranslation')->where('language_code', '=', $language)->get();
        if(sizeof($translation)>0){
            $this->setAttribute('name',$translation[0]->name);
            $this->setAttribute('description',$translation[0]->description);
            return true;
        } else {
            return false;
        }
    }
}
