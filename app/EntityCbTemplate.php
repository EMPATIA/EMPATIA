<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EntityCbTemplate extends Model
{
    protected $fillable = [
        'cb_key',
        'entity_id',
        'cb_type_id',
        'name'
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
}
