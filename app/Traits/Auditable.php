<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

trait Auditable
{
    public static function bootAuditable()
    {
        static::creating(function ($model) {
            if (Schema::hasColumn($model->table, 'created_by') || self::hasSchemaColumn($model, 'created_by')) {
                $model->created_by = Auth::id() ?? 1;
                $model->updated_by = Auth::id() ?? 1;
            }
            $previous = last(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 14));
            self::logger($previous,$model);
        });
        static::deleting(function ($model) {
            if (Schema::hasColumn($model->table, 'deleted_by') || self::hasSchemaColumn($model, 'deleted_by')) {
                $model->deleted_by = Auth::id() ?? 1;
                $model->save();
            }
            $previous = last(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 7));
            self::logger($previous,$model);
        });
        static::updating(function ($model) {
            if (Schema::hasColumn($model->table, 'updated_by') || self::hasSchemaColumn($model, 'updated_by')) {
                $model->updated_by = Auth::id() ?? 1;
            }
            $previous = last(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 9));
            self::logger($previous,$model);
        });
        static::restored(function ($model) {
            if (Schema::hasColumn($model->table, 'deleted_by') || self::hasSchemaColumn($model, 'deleted_by')) {
                $model->deleted_by = null;
                $model->save();
            }
            $previous = last(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 6));
            self::logger($previous,$model);
        });
    }

    public static function hasSchemaColumn(Model $model, string $column): bool
    {
        return method_exists(static::class, 'bootSushi') && isset($model->getSchema()[$column]);
    }

    private static function logger($previous,$model){
        $class = Str::of($previous["class"] ?? '')->explode('\\')->last();
        $function = $previous["function"];
        $facility = (new \ReflectionClass($model))->getShortName();
        $message = json_encode($model);
        auditLog($message, $facility, $function, $class);
    }
}
