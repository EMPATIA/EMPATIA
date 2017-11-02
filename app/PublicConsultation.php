<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PublicConsultation extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'cb_key',
        'public_consultation_key',
        'entity_id'
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
     * A Public Consultation belongs to one Entity
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function entity(){
        return $this->belongsTo('App\Entity');
    }
}
