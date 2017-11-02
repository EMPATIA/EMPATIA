<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Kiosk
 * @package App
 */
class KioskProposal extends Model
{
    use SoftDeletes;

    protected $fillable = ['proposal_key' ,'position'];    
    
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
     * Each KioskType belongs to a Kiosk.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function kiosk() {
        return $this->belongsTo('App\Kiosk');
    }     
}
