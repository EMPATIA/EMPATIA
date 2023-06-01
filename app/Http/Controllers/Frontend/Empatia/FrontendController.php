<?php

namespace App\Http\Controllers\Frontend\Empatia;

use App\Helpers\HBackend;
use App\Http\Controllers\Frontend\Empatia\Cbs\CbsController;
use App\Models\Empatia\Cbs\Cb;
use App\Models\Empatia\Cbs\Topic;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Database\QueryException;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class FrontendController extends Controller
{
    /**
     * Displays page by slug.
     *
     * @param string $slug Slug
     * @param string|null $params
     * @return mixed
     */
    public static function pageBySlug(string $slug = 'home', string $params = null)
    {
        try {
            $request = request();
            $request->merge(compact('slug', 'params'));

            $paramSegments = explode('/', $params ?? '');
            $paramRemainingSegments = $paramSegments;
            $mustRedirect = false;
            $redirectUrlParts = [];

            /**   Handle CbType   **/

            $cbTypes = getField(HBackend::getConfigurationByCode('cb_settings'), 'types');
            $cbType = null;

            foreach ($cbTypes as $key => $type) {
                if (getField($type, 'slug.' . getLang()) == $slug) {
                    $cbType = $key;
                    $redirectUrlParts[] = $slug;

                    if ( empty(reset($paramRemainingSegments)) && !getField($type, 'skipIndex') ) {
                        return $mustRedirect
                            ? redirect(route('page', [implode('/',$redirectUrlParts)]), 301)
                            : CbsController::showCbType($cbType);
                    }

                    break;
                }
            }

            // if cb type slug not found in current lang
            if( !$cbType ){
                foreach ($cbTypes as $key => $type) {
                    $break = false;

                    foreach (getLanguagesFrontend() as $language) {
                        $localeSlug = data_get($type, 'slug.' . $language['locale']);

                        if ( $localeSlug == $slug ) {

                            $cbType = $key;
                            $currentLocaleSlug = data_get($type, 'slug.' . getLang(), $localeSlug);
                            $redirectUrlParts[] = $currentLocaleSlug;

                            if ( empty(reset($paramRemainingSegments)) && !getField($type, 'skipIndex') ) {
                                return ( $currentLocaleSlug != $slug )
                                    ? redirect(route('page', [implode('/',$redirectUrlParts)]), 301)
                                    : CbsController::showCbType($cbType);
                            }

                            $mustRedirect = $currentLocaleSlug !== $localeSlug;
                            $break = true;
                            break;
                        }
                    }

                    if( $break ) break;
                }
            }

            /**   Handle Cb   **/

            $cbSlug = array_shift($paramRemainingSegments);
            $cb = !empty($cbSlug) ? Cb::where('slug->' . getLang(), $cbSlug)->whereType($cbType)->first() : null;
            if( $cb ){
                $redirectUrlParts[] = $cbSlug;
            }

            // if cb slug not found in current lang
            if (!$cb && !empty($cbSlug)) {
                foreach (getLanguagesBackend() as $language) {
                    $cb = Cb::where('slug->' . $language['locale'], $cbSlug)->whereType($cbType)->first();
                    $localeSlug = data_get($cb, 'slug.' . $language['locale']);

                    if ( $cb ) {
                        $currentLocaleSlug = data_get($cb, 'slug.' . getLang(), $localeSlug);
                        $redirectUrlParts[] = $currentLocaleSlug;

                        if ( empty(reset($paramRemainingSegments)) ) {
                            return ($currentLocaleSlug != $cbSlug || $mustRedirect)
                                ? redirect(route('page', [implode('/',$redirectUrlParts)]), 301)
                                : CbsController::showCb($cb, $params);
                        }

                        $mustRedirect = $cbSlug !== $currentLocaleSlug;
                        break;
                    }
                }
            }

            // cb actions
            $action = reset($paramRemainingSegments) ?: 'show';
            $redirectUrlParts[] = $action;

            if ($cb && $cb->canShowInFrontend()) {
                if( $mustRedirect ){
                    return redirect(route('page', [implode('/',$redirectUrlParts)]), 301);
                }

                if ( in_array($action, CbsController::$cbActions) ) {
                    return CbsController::showCb($cb, $params);
                } elseif ( $action == "create" ) {
                    return CbsController::showTopic($cb, null, $params);
                }
            }

            array_pop($redirectUrlParts);

            /**   Handle Topic   **/

            $topicSlug = array_shift($paramRemainingSegments);
            $topic = !empty($topicSlug) ? Topic::where('slug->' . getLang(), $topicSlug)->whereCbId($cb->id)->first() : null;
            if( $topic ){
                $redirectUrlParts[] = $topicSlug;
            }

            if (!$topic && !empty($topicSlug)) {
                foreach (getLanguagesFrontend() as $language) {
                    $topic = Topic::where('slug->' . $language['locale'], $topicSlug)->whereCbId($cb->id)->first();
                    $localeSlug = data_get($topic, 'slug.' . $language['locale']);

                    if ( $topic ) {
                        $currentLocaleSlug = data_get($topic, 'slug.' . getLang(), $localeSlug);
                        $redirectUrlParts[] = $currentLocaleSlug;

                        if ( empty(reset($paramRemainingSegments)) ) {
                            return ($currentLocaleSlug != $topicSlug || $mustRedirect)
                                ? redirect(route('page', [implode('/',$redirectUrlParts)]), 301)
                                : CbsController::showTopic($cb, $topic, $params);
                        }

                        $mustRedirect = $topicSlug !== $currentLocaleSlug;
                        break;
                    }
                }
            }

            // topic actions
            $action = reset($paramRemainingSegments) ?: 'show';
            $redirectUrlParts[] = $action;

            if ($topic /* && other FE conditions */) {
                if( $mustRedirect ){
                    return redirect(route('page', [implode('/',$redirectUrlParts)]), 301);
                }

                if ( in_array($action, CbsController::$topicActions) ) {
                    return CbsController::showTopic($cb, $topic, $params);
                }
            }

            array_pop($redirectUrlParts);

        } catch (QueryException | Exception  | \Throwable $e) {
            logError($e->getMessage() .' at line '. $e->getLine());
            abort( http_status_code_from_exception($e) );
        }

        return null;
    }
}
