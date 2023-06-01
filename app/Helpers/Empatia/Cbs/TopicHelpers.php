<?php

namespace App\Helpers\Empatia\Cbs;


use App\Helpers\HBackend;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\Backend\Entities\Configuration;
use Modules\Backend\Helpers\HForm;
use App\Http\Controllers\Backend\ConfigurationsController;
use App\Models\Empatia\Cbs\Cb;
use App\Models\Empatia\Cbs\Topic;
use App\Http\Controllers\Backend\Empatia\Cbs\CbsController;
use App\Http\Controllers\Backend\Empatia\Cbs\TopicsController;
use Modules\Files\Http\Controllers\FilesController;
use Modules\Frontend\Helpers\CvHelpers;
use App\Helpers\HFrontend;
use Route;


class TopicHelpers
{
    private static string $defaultStatusConfig = 'topic_status';

    public static function getFormActionAndMethod($cbId = 0, $id = 0, $action = null): array
    {
        if ((new HForm)->isCreate($action)) {
            return [
                'action' => route('backend.topics.store', ['cbId' => $cbId]),
                'method' => 'POST'
            ];
        } else if ((new HForm)->isEdit($action)) {
            return [
                'action' => route('backend.topics.update', ['cbId' => $cbId, 'id' => $id]),
                'method' => 'PUT'
            ];
        } else {
            return [
                'action' => '',
                'method' => ''
            ];
        }
    }

    public static function getActionCancel($cbId = 0, $id = 0, $action = null): string
    {
        if ((new HForm)->isEdit($action)) {
            return action([TopicsController::class, 'show'], ['cbId' => $cbId, 'id' => $id]);
        } else {
            return self::getActionIndex($cbId);
        }
    }

    public static function getActionIndex($cbId): string
    {
        return action([CbsController::class, 'show'], ['id' => $cbId]);
    }

    public static function getActionCreate($cbId): string
    {
        return action([TopicsController::class, 'create'], ['cbId' => $cbId]);
    }

    public static function getActionEdit($cbId, $id = 0): string
    {
        return action([TopicsController::class, 'edit'], ['cbId' => $cbId, 'id' => $id]);
    }

    public static function getActionDelete($cbId, $id = 0): string
    {
        return action([TopicsController::class, 'destroy'], ['cbId' => $cbId, 'id' => $id]);
    }

    public static function getActionRestore($cbId, $id = 0): string
    {

        return action([TopicsController::class, 'restore'], ['cbId' => $cbId, 'id' => $id]);
    }

    public static function getVersionString(Topic $topic, int $ver = null, $addVersion = true): string
    {
        $str = '';

        try {
            if (empty($ver)) $ver = $topic->version ?? 0;

            $version = $topic->versions->$ver ?? null;

            if (empty($version)) return "v0";

            $userId = $version->user_version;
            $date = Carbon::parse($version->date_version)->format('Y-m-d H:i');

            if ($addVersion)
                $str .= "v" . $version->version . ": ";

            $str .= $date . " (" . getUserName($userId) . ")";
        } catch (QueryException|Exception|\Throwable $e) {
            logError(json_encode($e->getMessage()));
            $str = "-- ERROR --";
        }

        return $str;
    }

    public static function getCb($cbId): object
    {
        if (empty($cbId))
            return (object)[];

        try {
            return Cb::findOrFail($cbId);
        } catch (QueryException|Exception|\Throwable $e) {
            logError('content config: ' . json_encode($e->getMessage()));
        }

        return (object)[];
    }

    public static function getCbByCode($code): ?Cb
    {
        if (empty($code))
            return null;

        try {
            return Cb::select('id', 'type', 'template', 'code', 'title', 'start_date', 'end_date', 'content', 'slug', 'parameters', 'data')->whereCode($code)->first();
        } catch (QueryException|Exception|\Throwable $e) {
            logError('getCbByCode( ' . $code . ' ): ' . json_encode($e->getMessage()));
        }

        return null;
    }

    public static function getTopicParameters($parameters)
    {
        try {
            foreach ($parameters as $parameter) {
                if ($parameter->type == 'text' || $parameter->type == 'textarea') {
                    if (!empty($parameter->value) && empty($parameter->deleted_at)) {
                        $arrayKey = [];
                        $arrayValue = [];
                        foreach ($parameter->value as $val) {
                            if (is_array($val) || is_object($val)) {
                                foreach ($val as $key => $v) {
                                    $arrayKey[$key] = $key;
                                    $arrayValue[$key] = $v;
                                }
                            }
                        }
                        $parameter->value = array_combine($arrayKey, $arrayValue);
                    }
                }
            }
            return (array)$parameters;
        } catch (\Exception $e) {
            dd($e);
        }
    }

    public static function getParametersObject($topic, $cb = null): ?object
    {
        if (!$topic instanceof Topic)
            return null;
        if ($cb != null && !$cb instanceof Cb)
            return null;

        if ($cb == null)
            $cb = Cb::find($topic->cb_id);

        $parameters = [];

        foreach ($cb->parameters ?? [] as $cb_param) {
            $topic_param = $topic->parameters->{$cb_param->id ?? null} ?? null;

            if (!empty($cb_param->code) && !empty($topic_param))
                $parameters[$cb_param->code] = $topic_param;
        }

        if (empty($parameters))
            $parameters = null;

        return (object)$parameters;
    }

    public static function getCoverImage($topic, Cb $cb = null): ?object
    {
        try {
            if (!$topic instanceof Topic)
                return null;

            if (!$cb) {
                $site = request()->site;
                if (!$site)
                    throw new Exception("Could not get site");

                $cb = TopicHelpers::getCbByCode(config("sites.$site->code.cb"));
                if (!$cb)
                    throw new Exception("Could not get cb '" . config("sites.$site->code.cb") . "', 'sites.$site->code.cb'");
            }
            $image = \Cache::remember('cache_topic_cover_image_' . $cb->id . '_' . $topic->id, (env('FILE_TEMP_URL_LIFESPAN', 10080) * 60), function () use ($cb, $topic) {
                logDebug("[CB: " . $cb->id . "][T: " . $topic->id . "] Cover image not in cache");

                try {
//                    $imgTopicParam = $topic->parameters->{$imgCbParam->id} ?? null;
//                    if( !$imgTopicParam )
//                        throw new Exception("Could not get topic parameter");

                    $imgCbParam = findObjectByProperty('code', 'cover_img', $cb->parameters);
                    if (!$imgCbParam) {
                        logDebug("[CB: " . $cb->id . "][T: " . $topic->id . "] Could not get cb parameter");
                        return "no-image";
                    }

                    $imgTopicParam = $topic->parameters->{$imgCbParam->id} ?? null;
                    if (!$imgTopicParam) {
                        logDebug("[CB: " . $cb->id . "][T: " . $topic->id . "] Could not get topic parameter");
                        return "no-image";
                    }
                    $imgTopicParam = json_decode($imgTopicParam->{getLang()} ?? null);
                    if (is_array($imgTopicParam) && !empty($imgTopicParam))
                        $imgTopicParam = $imgTopicParam[0];
                    if (is_string($imgTopicParam)) {
                        $imgTopicParam = json_decode($imgTopicParam);
                        if (is_array($imgTopicParam) && !empty($imgTopicParam)) {
                            $imgTopicParam = $imgTopicParam[0];
                        }
                    }

                    $image = FilesController::getImage($imgTopicParam->id ?? '');
                    if (!$image) {
                        logDebug("[CB: " . $cb->id . "][T: " . $topic->id . "] Could not get image");
                        return "no-image";
                    }

                    if ($image instanceof Exception) {
                        logError("[CB: " . $cb->id . "][T: " . $topic->id . "] Error: " . $image->getMessage());
                        return "no-image";
                    }

                    return (object)$image;
                } catch (Exception $e) {
                    logError("[CB: " . $cb->id . "][T: " . $topic->id . "] Error: " . $e->getMessage());
                    return "no-image";
                }
            });

            if ($image == "no-image") return null;
            return $image;

        } catch (Exception $e) {
            logError("Error: " . $e->getMessage());
            return null;
        }
    }

    public static function getStatusByCb(Cb $cb): ?object
    {
        try {
            return getField(HBackend::getConfigurationByCode( config('empatia.cbs.settings_config', 'cb_settings') ), "types.$cb->type.topic_status");

        } catch (QueryException|Exception|\Throwable $e) {
            logError('status config: ' . json_encode($e->getMessage()));
        }

        return (object)[];
    }

    public static function getStateById(int $id, Cb $cb): ?object
    {
        try {
            $status = self::getStatusByCb($cb);

            return findObjectByProperty('id', $id, $status);

        } catch (QueryException | Exception | \Throwable $e) {
            logError( $e->getMessage() );
        }
        return null;
    }

    public static function getStateByCode(string $code, Cb $cb): ?object
    {
        try {
            $status = self::getStatusByCb($cb);

            return getField($status, $code, null);

        } catch (QueryException | Exception | \Throwable $e) {
            logError( $e->getMessage() );
        }
        return null;
    }

    public static function getStateName(int $id, Cb $cb): ?string
    {
        try {
            $state = self::getStateById($id, $cb);

            return getField($state, 'title.'.getLang());

        } catch (Exception $e) {
            logError( $e->getMessage() );
        }

        return null;
    }

    public static function getTopicsFromCbByPos($cbId, $trashed)
    {
        if ($trashed)
            return Topic::where('cb_id', '=', 2)->withTrashed()->orderBy('position')->get();
        else
            return Topic::where('cb_id', '=', 2)->orderBy('position')->get();

    }

    public static function getTopicByCb($cb)
    {
        if (empty($cb))
            return null;
        return Topic::whereCbId($cb)->first();
    }

    public static function getTopicById($id)
    {
        if (empty($id))
            return null;

        if ($topic = Topic::where('id', $id)->get()->first()) {

            return $topic;
        }


        return null;
    }


    public static function getParamValues($topics, $id, $all)
    {
        $names = self::getParamNames($all);
        $count = [];

        foreach ($names as $key => $val) {
            $count[$key] = 0;
            foreach ($topics as $topic) {

                if (isset($topic->parameters->{$id}) && ($topic->parameters->{$id}->{getLang()})) {
                    $v = self::getParamByID($topic->parameters->{$id}->{getLang()}, $all);
                    if ($v == $key) {
                        $count[$key]++;
                    }
                }
            }
        }

        return $count;
    }


    public static function getParamsPercentage($count, $total)
    {
        $percentage = [];
        foreach ($count as $v) {
            $percentage[] = round(($v * 100) / $total);
        }
        return $percentage;
    }

    public static function getParamByID($id, $all)
    {

        foreach ($all as $a) {
            if ($a->id == $id)
                return $a->value->{getLang()};
        }
    }

    public static function getParamNames($all)
    {
        $names = [];
        foreach ($all as $a) {
            $names[$a->value->{getLang()}] = null;
        }
        return $names;
    }


    /**
     * Whether a topic can be created or not
     * @param Cb|null $cb
     * @return bool
     */
    public static function formActionRoute(string $action, Cb $cb = null): ?string
    {
        if( !in_array($action, ['create', 'edit']) ){
            return null;
        }

        if( $action == 'create' ){
            return 'frontend.topics.store';
        }
        if( $action == 'edit' ){
            return 'frontend.topics.update';
        }

        return null;
    }

    /**
     * Whether a topic can be created or not
     * @param Cb|null $cb
     * @return bool
     */
    public static function canCreate(Cb $cb = null): bool
    {
        try {
            if( empty($cb) ){
                logDebug('FALSE: topic cb not set');
                return false;
            }

            $user = auth()->user();

            if( $user && $user->hasAnyRole(['admin', 'laravel-admin']) ){
                return true;
            }

            // if cb is not ongoing
            if( !$cb->isOngoing() && isFrontend() ){
                logDebug('FALSE: cb not ongoing');
                return false;
            }

            $createSettings = data_get($cb, config('empatia.cbs.settings.topics.create'));
            if( empty($createSettings) ){
                if( isFrontend() ){
                    logDebug('FALSE: empty cb settings');
                }
                return !isFrontend();
            }

            if( !data_get($createSettings, 'enabled') ){
                return false;
            }

            // if topic create phase not ongoing
            if( !$cb->isTopicCreateOngoing() ){
                logDebug('FALSE: topic create not ongoing');
                return false;
            }

//            // if exists in a table
//            if( !empty($user->getCbTable($cb)) && $user->canAny(['create-topic-in-any-table','create-topic-in-own-table']) ){
//                return true;
//            }
//
//            if( !empty($user->getCbTable($cb)) && $user->can('create-any-topic') ){
//                return true;
//            }

        } catch ( Exception $e ) {
            logError( $e->getMessage() .' at line '. $e->getLine() );
        }

        return false;
    }

    /**
     * Whether a topic can be edited or not
     * @param Topic|null $topic
     * @return bool
     */
    public static function canEdit(Topic $topic = null): bool
    {
        try {
            if( empty($topic) ){
                logDebug('FALSE: topic not set');
                return false;
            }

            $user = auth()->user();

            if( $user && $user->hasRole(['admin']) ){
                return true;
            }

            if( empty($topic->cb) ){
                logDebug('FALSE: topic cb not set');
                return false;
            }

            // if cb is not ongoing
            if( !$topic->cb->isOngoing() && HFrontend::isPublicRequest() ){
                logDebug('FALSE: cb not ongoing');
                return false;
            }

            $editSettings = data_get($topic->cb, config('empatia.cbs.settings.topics.edit'));
            if( empty($editSettings) ){
                if( HFrontend::isPublicRequest() ){
                    logDebug('FALSE: empty cb settings');
                }
                return !HFrontend::isPublicRequest();
            }

            if( !data_get($editSettings, 'enabled') ){
                logDebug('FALSE: topic edit not enabled in cb settings');
                return false;
            }

            // if topic create phase not ongoing
            if( !$topic->cb->isTopicCreateOngoing() ){
                logDebug('FALSE: topic create not ongoing');
                return false;
            }

            // if its own topic
            if( $topic->isOwn() && $user->can('edit-own-topics') ){
                return true;
            }

            // if belongs to topic table
            if( (($topic->getCbTable() ?? false) === $user->getCbTable($topic->cb)) && $user->can('edit-own-table-topics') ){
                return true;
            }

            if( $user->can('edit-any-topics') ){
                return true;
            }

        } catch ( Exception $e ) {
            logError( $e->getMessage() .' at line '. $e->getLine() );
        }

        return false;
    }

    /**
     * Whether a topic can be deleted or not
     * @param Topic $topic
     * @return bool
     */
    public static function canDelete(Topic $topic = null): bool
    {
        try {
            if( empty($topic) ){
                logDebug('FALSE: topic not set');
                return false;
            }

            $user = auth()->user();

            if( $user && $user->hasRole(['admin']) ){
                return true;
            }

            if( empty($topic->cb) ){
                logDebug('FALSE: topic cb not set');
                return false;
            }

            // if cb is not ongoing
            if( !$topic->cb->isOngoing() && HFrontend::isPublicRequest() ){
                logDebug('FALSE: cb not ongoing');
                return false;
            }

            $deleteSettings = getField($topic->cb, config('empatia.cbs.settings.topics.delete'));
            if( empty($deleteSettings) ){
                if( HFrontend::isPublicRequest() ){
                    logDebug('FALSE: empty cb settings');
                }
                return !HFrontend::isPublicRequest();
            }

            if( !data_get($deleteSettings, 'enabled') ){
                logDebug('FALSE: topic delete not enabled in Cb settings');
                return false;
            }

            // if cb is inaccessible or topic create phase not ongoing
            if( !$topic->cb->isTopicCreateOngoing() ){
                logDebug('FALSE: topic create not ongoing');
                return false;
            }

            // if its own topic
            if( $topic->isOwn() && $user->can('delete-own-topics') ){
                return true;
            }

            // if belongs to topic table
            if( (($topic->getCbTable() ?? false) === $user->getCbTable($topic->cb)) && $user->can('delete-own-table-topics') ){
                return true;
            }

            if( $user->can('delete-any-topics') ){
                return true;
            }

        } catch ( Exception $e ) {
            logError( $e->getMessage() .' at line '. $e->getLine() );
        }

        return false;
    }

    public static function getAllProponentsDetails($topic)
    {
        try {
            $proponentsDetails = [];
            foreach (getField($topic, "proponents", []) as $proponent) {
                $proponentData = json_decode(getField($proponent, 'data'));

                $user = User::whereId(getField($proponent, "user_id"))->first();

                if (isset($user)) {
                    $proponentsDetails[] = [
                        'name' => getField($user, 'name') ?? '-',
                        'email' => getField($user, 'email') ?? '-',
                        'role' => getField($proponentData, 'role') ?? '-',
                        'userId' => getField($proponent, "user_id")
                    ];
                }
            }
            return $proponentsDetails;

        } catch (Exception $e) {
            logError($e->getMessage());
        }

        return null;
    }

}

