<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Content
 * @package App
 */
class Content extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'content_key',
        'type_id',
        'content_type_type_id',
        'fixed',
        'highlight',
        'clean',
        'start_date',
        'end_date',
        'publish_date'
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
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Each Content has many Page Contents
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function contentTranslations()
    {
        return $this->hasMany('App\ContentTranslation');
    }

    /**
     * @param null $language
     * @param null $version
     * @return bool
     */
    public function translation($language = null, $version = null)
    {
        if (is_null($version)) {
            $translation = $this->hasMany('App\ContentTranslation')->whereLanguageCode($language)->whereEnabled(1)->get();
        } else {
            $translation = $this->hasMany('App\ContentTranslation')->whereLanguageCode($language)->whereVersion($version)->get();
        }
        if (sizeof($translation) > 0) {
            if (!empty($translation[0]->title) || (!empty($translation[0]->content))){
                $this->setAttribute('title', $translation[0]->title);
                $this->setAttribute('summary', $translation[0]->summary);
                $this->setAttribute('content', $translation[0]->content);
                $this->setAttribute('content_id', $translation[0]->content_id);
                $this->setAttribute('version', $translation[0]->version);
                $this->setAttribute('link', $translation[0]->link);
                $this->setAttribute('enabled', $translation[0]->enabled);
                $this->setAttribute('created_by', $translation[0]->created_by);
                $this->setAttribute('docs_main', $translation[0]->docs_main);
                $this->setAttribute('docs_side', $translation[0]->docs_side);
                $this->setAttribute('highlight', $translation[0]->highlight);
                $this->setAttribute('slideshow', $translation[0]->slideshow);

                return true;
            }
        }
        return false;
    }

    /**
     * @param null $version
     * @return mixed
     */
    public function translations($version)
    {
        if (is_null($version)) {
            $contentVersion = $this->hasMany('App\ContentTranslation')
                ->orderBy('version', 'desc')
                ->select('version')
                ->first();
            $translations = $this->hasMany('App\ContentTranslation')->whereVersion($contentVersion->version)->get();
        } else {
            $translations = $this->hasMany('App\ContentTranslation')->whereVersion($version)->get();
        }
        $this->setAttribute('translations', $translations);
        return $translations;
    }

    /**
     * Each Content has many Files
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function contentFiles()
    {
        return $this->hasMany('App\ContentFile');
    }

    /**
     * Each Content has one ContentType
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function contentType()
    {
        return $this->belongsTo('App\ContentType');
    }

    /**
     * Each Content may belongs to one ContentTypeType
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function contentTypeType()
    {
        return $this->belongsTo('App\ContentTypeType');
    }


    public function newTranslation($language = null, $languageDefault = null, $version= null)
    {
        if (is_null($version)) {
            $translation = $this->hasMany('App\ContentTranslation')->whereEnabled(1)->orderByRaw("FIELD(language_code,'".$languageDefault."','".$language."')DESC")->get();
        } else {
            $translation = $this->hasMany('App\ContentTranslation')->whereVersion($version)->orderByRaw("FIELD(language_code,'".$languageDefault."','".$language."')DESC")->get();
        }
        if (sizeof($translation) > 0) {
            if (!empty($translation[0]->title) || (!empty($translation[0]->content))){
                $this->setAttribute('title', $translation[0]->title);
                $this->setAttribute('summary', $translation[0]->summary);
                $this->setAttribute('content', $translation[0]->content);
                $this->setAttribute('content_id', $translation[0]->content_id);
                $this->setAttribute('version', $translation[0]->version);
                $this->setAttribute('link', $translation[0]->link);
                $this->setAttribute('enabled', $translation[0]->enabled);
                $this->setAttribute('created_by', $translation[0]->created_by);
                $this->setAttribute('docs_main', $translation[0]->docs_main);
                $this->setAttribute('docs_side', $translation[0]->docs_side);
                $this->setAttribute('highlight', $translation[0]->highlight);
                $this->setAttribute('slideshow', $translation[0]->slideshow);
                $this->setAttribute('lang',$translation[0]->language_code );
            }
        }
    }


}
