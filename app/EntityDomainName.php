<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EntityDomainName extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'entity_id',
        'domain_title',
        'domain_name'
    ];

    /**
     * An Entity Domain Name belongs to one Entity
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function entity(){
        return $this->belongsTo('App\Entity');
    }
}
