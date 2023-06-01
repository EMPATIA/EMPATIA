<?php

namespace App\Models\Backend\CMS;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use App\Traits\Auditable;
use App\Models\Backend\CMS\ContentUsers;

class Content extends Model
{
    use HasFactory, SoftDeletes, HasTimestamps, Auditable;

    protected $guarded = [];

    protected $casts = [
        'title' => 'object',
        'options' => 'object',
        'sections' => 'object',
        'versions' => 'object',
        'slug' => 'object',
        'seo' => 'object',
    ];

    public function section(string $code)
    {
        foreach($this->sections ?? [] as $section) {
            if(data_get($section, "code") == $code) return $section;
        }

        return null;
    }

    public function sectionValue(string $code, mixed $default = null, string $lang = null) : ?string
    {
        $value = null;

        try {
            $value = data_get($this->section($code) ?? [], 'value.'.getLang(), $default, $lang);
        } catch (QueryException | Exception  | \Throwable $e) {
            logError("Error getting section value: ".$e->getMessage());
        }

        return $value;
    }
}
