<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EntityNotification extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'groups',
        'active',
        'entity_id',
        'template_key',
        'entity_notification_key',
        'entity_notification_type_id',
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
     * An EntityNotification belongs to one EntityNotificationType
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function entityNotificationType(){
        return $this->belongsTo('App\EntityNotificationType');
    }

    /**
     * An EntityNotification belongs to one Entity
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function entity(){
        return $this->belongsTo('App\Entity');
    }
}
