<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccessMenu extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'entity_id',
        'site_id',
        'name',
        'description',
        'active',
        'created_by',
        'updated_by'
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
     * A Access Menu as one Access Type
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function accessType(){
        return $this->belongsTo('App\AccessType');
    }

    /**
     * A Access Page as one Entity
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function entity(){
        return $this->belongsTo('App\Entity');
    }

    /**
     * A Access Page has one Site
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function site(){
        return $this->belongsTo('App\Site');
    }
}
