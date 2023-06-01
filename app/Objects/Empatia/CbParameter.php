<?php

namespace App\Objects\Empatia;

use App\Objects\Parameter;

#[AllowDynamicProperties]
class CbParameter extends Parameter
{
    public function isFilter() : bool
    {
        return data_get($this, 'flags.use_as_filter') == true;
    }

    public function isFilterEnabled() : bool
    {
        return data_get($this, 'filter_settings.enabled') == true;
    }

    public function isFilterMultiple() : bool
    {
        return data_get($this, 'filter_settings.multiple') == true;
    }

    public function isFilterCumulative() : bool
    {
        return data_get($this, 'filter_settings.cumulative') == true;
    }
}
