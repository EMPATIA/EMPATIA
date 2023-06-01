<?php

namespace App\Models\Empatia\Cbs;

use App\Traits\Auditable;
use App\Traits\WithBlamestamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\SushiModel;

class OperationSchedule extends Model
{
    use SushiModel, SoftDeletes, WithBlamestamps, Auditable;

    protected $primaryKey = 'code';
    public $incrementing = false;
    protected $keyType = 'string';

    public $timestamps = true;

    protected $schema = [
        'code' => 'string',
        'description' => 'string',
        'start_date' => 'date',
        'end_date' => 'date',
        'enabled' => 'boolean',
    ];

    protected $fillable = ['code', 'description', 'start_date', 'end_date', 'enabled'];

    /**
     * Checks whether an operation schedule is enabled.
     * @return bool
     */
    public function isEnabled(): bool
    {
        return data_get($this, 'enabled') === true;
    }

    /**
     * Checks whether an operation schedule is ongoing.
     * @return bool
     */
    public function isOngoing(): bool
    {
        $now = now();

        $startDate = !empty($startDate = data_get($this, 'start_date')) ? carbon($startDate) : null;
        $endDate = !empty($endDate = data_get($this, 'end_date')) ? carbon($endDate) : null;

        // if not started yet
        if (empty($startDate) || $startDate > $now) {
            return false;
        }

        // if already ended
        if (!empty($endDate) && $endDate < $now) {
            return false;
        }

        // at this point, it's certain it's ongoing

        return true;
    }

    /**
     * Checks whether an operation schedule is enabled and ongoing.
     * @param string $code The operation schedule code
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->isEnabled() && $this->isOngoing();
    }

    /**
     * Checks whether an operation schedule has started.
     * @param string $code The operation schedule code
     * @return bool
     */
    public function hasStarted(string $code): bool
    {

        $now = now();
        $startDate = !empty($startDate = data_get($this, 'start_date')) ? carbon($startDate) : null;

        return !empty($startDate) && $now >= $startDate;
    }

    /**
     * Checks whether an operation schedule has ended.
     * @param string $code The operation schedule code
     * @return bool
     */
    public function hasEnded(string $code): bool
    {

        $now = now();
        $endDate = !empty($endDate = data_get($this, 'end_date')) ? carbon($endDate) : null;

        return !empty($endDate) && $now > $endDate;
    }
}
