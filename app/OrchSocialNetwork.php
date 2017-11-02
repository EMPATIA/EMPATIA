<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrchSocialNetwork extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'orch_social_networks';

    protected $fillable = [
        'social_network_key',
        'code',
        'app_secret',
        'app_id',
    ];

    protected $dates = ['deleted_at'];

    protected $hidden = ['deleted_at', 'id'];

    public function site(){
        return $this->belongsTo('App\Site');
    }
}
