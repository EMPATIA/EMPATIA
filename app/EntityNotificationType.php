<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EntityNotificationType extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'code'
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
    protected $hidden = ['id', 'deleted_at'];

    /**
     * An EntityNotificationType can have many EntityNotifications
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function entityNotifications(){
        return $this->hasMany('App\EntityNotification');
    }

    /**
     * @param null $language
     * @return bool
     */
    public function translation($language = null)
    {
        $translation = $this->hasMany('App\EntityNotificationTypeTranslation')->where('language_code', '=', $language)->get();
        if(sizeof($translation)>0){
            $this->setAttribute('value',$translation[0]->value);
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
        $translations = $this->hasMany('App\EntityNotificationTypeTranslation')->get();
        $this->setAttribute('translations',$translations);
        return $translations;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function entityNotificationTypeTranslations() {
        return $this->hasMany('App\EntityNotificationTypeTranslation');
    }
}
