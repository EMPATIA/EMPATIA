<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Kiosk extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['kiosk_key','entity_id', 'title','entity_cb_id','kiosk_type_id','event_key','created_by', 'updated_by'];

    /**
     * The attributes that should be hidden for arrays.
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
     * An Idea belongs to a Kiosk.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function idea(){
        return $this->belongsTo('App\Idea');
    }    
    
    /**
     * An Entity belongs to a Kiosk.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function entity(){
        return $this->belongsTo('App\Entity');
    }      
    
    /**
     * Each Kiosk has many Kiosk Types.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function kioskTypes() {
        return $this->hasMany('App\KioskType');
    }   
    
    /**
     * Each Kiosk has many Kiosk Proposals.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function proposals() {
        return $this->hasMany('App\KioskProposal');
    }       
     
    
    /**
     * An Entity Cb belongs to a Kiosk.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function entityCb(){
        return $this->belongsTo('App\EntityCb');
    }        
}
