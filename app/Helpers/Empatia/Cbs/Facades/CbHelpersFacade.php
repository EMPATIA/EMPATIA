<?php

namespace App\Helpers\Empatia\Cbs\Facades;

use Illuminate\Support\Facades\Facade;

class CbHelpersFacade extends Facade {
    public static function getFacadeAccessor() {
        return 'cb-helpers';
    }
}
