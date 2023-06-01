<?php

namespace App\Models\Empatia\Cbs;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Auditable;

class Vote extends Model
{
    use HasFactory, SoftDeletes, Auditable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'empatia_votes';

    protected $guarded = [
        'id',
        'created_by',
        'created_at',
        'updated_by',
        'updated_at',
        'deleted_by',
        'deleted_at',
    ];

    protected $casts = [
        'data' => 'object',
        'versions' => 'object'
    ];

    public function topic()
    {
        return $this->belongsTo(Topic::class);
    }

}
