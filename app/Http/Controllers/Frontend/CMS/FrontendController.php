<?php

namespace App\Http\Controllers\Frontend\CMS;

use App\Helpers\HBackend;
use App\Http\Controllers\Backend\CMS\MenusController;
use App\Http\Controllers\Frontend\Empatia\FrontendController as EmpatiaFrontendController;
use App\Models\Backend\CMS\Content;
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
     * @return Renderable
     */
    public function pageBySlug(string $slug = 'home', string $params = null)
    {
        try {
            /**   Handle Empatia Slug Processing   **/

            if( isVariant('empatia') ){
                $view = EmpatiaFrontendController::pageBySlug($slug, $params ?? '');

                if( !empty($view) ){
                    return $view;
                }
            }

            /**   Handle Content   **/

            $content = Content::where('slug->'.getLang(), $slug)->where('status', 'published')->first();

            if(!empty($content)) {
                // Return slug content
                return view('frontend.page', compact('slug', 'content'));
            } else {
                logDebug("Content not found: ".$slug);

                // Search content in another language
                foreach(getLanguagesFrontend() as $language) {
                    $content = Content::where('slug->' . $language['locale'], $slug)->where('status', 'published')->first();

                    if(!empty($content) && getFieldLang($content, "slug", null) != null) {
                        logDebug("Content found: ".$slug." | ".$language['locale']." | => ".getFieldLang($content, "slug"));

                        logDebug("Redirecting to => ".getFieldLang($content, "slug"));
                        return redirect()->action([self::class, 'pageBySlug'], ['slug' => getFieldLang($content, "slug")]);
                    }
                }

                /********************************************
                 * ADD CUSTOM MODULES SLUG MANAGEMENT
                 */


                /********************************************/
            }
        } catch (QueryException | Exception  | \Throwable $e) {
            logError(json_encode($e->getMessage()));
            abort( http_status_code_from_exception($e) );
        }

        abort(404);
    }

    /**
     * Displays page by slug.
     *
     * @param string $slug Slug
     * @return Renderable
     */
    public function newsBySlug($slug)
    {
        try {
            $content = Content::where('slug->'.getLang(), $slug)->where('status', 'published')->first();

            if(!empty($content)) {
                // Return slug content
                return view('frontend.page', compact('slug', 'content'));
            } else {
                logDebug("Content not found: ".$slug);

                // Search content in another language
                foreach(HBackend::getLanguages() as $language) {
                    $content = Content::where('slug->' . $language['locale'], $slug)->where('status', 'published')->first();

                    if(!empty($content)) {
                        logDebug("Content found: ".$slug." | ".$language['locale']." | => ".$content->slug->{getLang()});

                        logDebug("Redirecting to => ".$content->slug->{getLang()});
                        return redirect()->action([self::class, 'pageBySlug'], ['slug' => $content->slug->{getLang()}]);
                    }
                }

                /********************************************
                 * ADD CUSTOM MODULES SLUG MANAGEMENT
                 */


                /********************************************/
            }
        } catch (QueryException | Exception  | \Throwable $e) {
            logError(json_encode($e->getMessage()));
        }

        abort(404);
    }


    /**
     * Return news to list
     *
     */
    public static function getNews()
    {
        $news = Content::whereType('news')->whereStatus('published')->where('options->fields->date_publish','<=',\Carbon\Carbon::now()->format('Y-m-d'))->orderByDesc('options->fields->date_news')->get()->toArray();
        return $news;
    }

    /**
     * Fetch menu (default 'public') from database or cache
     *
     * @param string $menuType
     * @param int $parentId
     * @return array
     */
    public static function getMenu($content = null, $menuType = 'public', $parentId = 0) : array {
        // Use Backend controller to get menu structure
        $menus = MenusController::getMenuChildren($menuType, $parentId);
        $arr = [];

        foreach($menus ?? [] as $menu) {
            $link = getField($menu, "link".getLang());
            $active = false;
            if(!empty($link) && !empty($content->slug) && !empty($slug = $content->slug->{getLang()})){
                $active = $link === '/' . $slug;
            }
            // Use only viable public fields (hide remaining fields from public views)
            $arr[] = [
                'id' => $menu->id,
                'code' => $menu->code,
                'title' => getField($menu,"title.".getLang(), ''),
                'link' => getField($menu, "link.".getLang(),''),
                'options' => $menu->options ?? '',
                'children' => self::getMenu($content, $menuType, $menu->id),
                'active' => $active,
            ];
        }

        return $arr;
    }
}
