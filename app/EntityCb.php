<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EntityCb extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'cb_key',
        'entity_id',
        'cb_type_id'

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
     * An Entity Cb belongs to one CB Type
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function cbType(){
        return $this->belongsTo('App\CbType');
    }
    
    /**
     * An Entity CB can have many Kiosks.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function kiosks(){
        return $this->hasMany('App\Kiosk');
    }

    /**
     * An Entity Cb belongs to one Entity
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function entity(){
        return $this->belongsTo('App\Entity');
    }

    public function cb() {
        return $this->hasOne('App\Cb','cb_key','cb_key');
    }
}
