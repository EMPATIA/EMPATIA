<?php

namespace App\Helpers\Empatia\OperationSchedules;

use Illuminate\Database\QueryException;
use Modules\EMPATIA\Entities\OperationSchedules\OperationSchedule;

class OperationScheduleHelpers
{
    /**
     * Find and return an OperationSchedule by its code
     * @param   $code
     * @return  OperationSchedule|null
     */
    public static function getByCode($code): ?OperationSchedule
    {
        if (empty($code)) {
            return null;
        }

        try {
            return OperationSchedule::whereCode($code)->first();
        } catch ( QueryException|\Exception|\Throwable $e ) {
            logError('getByCode( ' . $code . ' ): ' . $e->getMessage());
        }

        return null;
    }
}
