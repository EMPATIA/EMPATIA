<?php

namespace App\Http\Controllers\Backend\CMS;

use Illuminate\Contracts\Translation\Loader;
use Illuminate\Translation\Translator;
use App\Helpers\HBackend;

class CustomTranslator extends Translator
{
    public function get($key, array $replace = [], $locale = null, $fallback = true)
    {
        $locale = $locale ?: (getlang());

        $translation = HBackend::getTranslation($locale, $key);

        return $this->makeReplacements($translation ?: $key, $replace);
    }
}

