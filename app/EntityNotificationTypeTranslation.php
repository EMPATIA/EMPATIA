<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EntityNotificationTypeTranslation extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'entity_notification_type_id',
        'language_code',
        'value',
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
     * An EntityNotificationTypeTranslation belongs to one EntityNotificationType
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function entityNotificationType(){
        return $this->belongsTo('App\EntityNotificationType');
    }
}
