<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class CbTranslation extends Model
{

    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['cb_id', 'code', 'language_code', 'status', 'translation'];

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
     * Each CbTranslation belongs to a Cb
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function cb() {
        return $this->belongsTo('App\Cb');
    }
}
