<?php

namespace App\Http\Controllers\Backend\CMS;

use App\Http\Controllers\Backend\Controller;
use App\Models\Empatia\Cbs\Cb;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Backend\CMS\Content;
use App\Helpers\HBackend;
use App\Helpers\HContent;

class ContentsController extends Controller
{
    // Controller prefix
    private $prefix = 'backend.cms.contents.';

    // Validation rules and messages
    private $validateRules = [];
    private $validateMessages = [];

    private $guarded = [
        'id',
        'created_by',
        'updated_by',
        'deleted_by',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public string $type;

    function __construct()
    {
//        parent::__construct();

        [$this->validateRules, $this->validateMessages] = HBackend::createControllerValidate([
            'title' => [
                'rules' => ['required', 'min:1', 'max:255'],
                'locale' => true,
            ],
        ], $this->prefix.'form.error');
    }

    /**
     * Display a listing of the resource.
     * @param $type
     * @return Renderable
     */
    public function index($type)
    {
        if($type != 'all')
            $this->validateType($type);

        return view($this->prefix.'index', [
            'title' => __($this->prefix. Str::slug($type) . '.index.title'),
            'type' => $type,
        ]);

    }

    /**
     * Show the form for creating a new resource.
     * @param $type
     * @return Renderable
     */
    public function create($type)
    {
        $this->validateType($type);

        return view($this->prefix.'create', [
            'title' => __($this->prefix. Str::slug($type) . "create.title"),
            'content' => [],
            'type' => $type,
            'configs' => HContent::getContentConfigurations($type)]);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @param $type
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request, $type)
    {
        $this->validateType($type);
        $request->validate($this->validateRules, $this->validateMessages);

        try {
            $data = $request->except($this->guarded);
            $data['type'] = $type;
            $data['status'] = 'unpublished';
            $data['version'] = '0';
            $data['slug'] = $this->createContentSlugs($data);
            $data['sections'] = $this->createContentSections($data);

            $data['seo'] = [];
            $data['seo']['title'] = $data['title->'.getLang()] ?? null;
            $data['seo']['og:site_name'] = config('app.name') ?? null;

            $content = Content::create($data);

            $content->save();

            logDebug('store: '.json_encode($content));
            flash()->addSuccess(__('backend.generic.store.ok'));
            return redirect()->action([self::class, 'show'], ['type' => $type, 'id' => $content->id]);
        } catch (QueryException | Exception  | \Throwable $e) {
            logError('store: '.json_encode($e->getMessage()));
            flash()->addError(__('backend.generic.store.error'));
            return redirect()->back()->withInput();
        }

    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($type, $id)
    {
        $this->validateType($type);
        $content = Content::whereId($id)->withTrashed()->firstOrFail();

        \Session::put('content_draft_'.$id, $content);

        return view($this->prefix.'content', [
            'title' => __($this->prefix. 'content-types.' . Str::slug($type)) . " :: ".getFieldLang($content, 'title'),
            'id' => $id,
            'type' => $type,
            'content' => $content,
            'status' => $content->status,
            'deleted' => !empty($content->deleted_at)
        ]);
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($type, $id)
    {
        $this->validateType($type);

        try {
            if(Content::findOrFail($id)->delete()) {
                session()->flash('success', __($this->prefix.'delete.ok'));
                return response()->json(['success' => 'success'], 200);
            }

            logError('delete error');
        } catch (QueryException | Exception  | \Throwable $e) {
            logError('delete: '.json_encode($e->getMessage()));
        }

        return redirect()->back()->withError(__($this->prefix.'delete.error'));

    }

    /**
     * Restore the specified resource from storage.
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function restore($type, $id)
    {
        $this->validateType($type);

        try {
            DB::beginTransaction();
            if (Content::withTrashed()->findOrFail($id)->restore()) {
                DB::commit();
                flash()->addSuccess(__('backend.generic.destroy.ok'));
                return response()->json(['success' => 'success'], 200);
            }
        } catch (QueryException|Exception|\Throwable $e) {
            DB::rollBack();
            logError('Restore: ' . json_encode($e->getMessage()) . ' at line ' . $e->getLine());
            flash()->addError(__('backend.generic.restore.error'));
            return redirect()->back()->withInput();
        }
    }
    /*******************************
     * Support methods
     */

    /**
     * Create content Slugs for all languages
     *
     * @param $data
     * @return object
     */
    public function createContentSlugs($data): object {
        $slugs = [];

        try {
            foreach(getLanguagesFrontend() as $language) {
                if(!empty($data["title->".$language['locale']])){
                    $slug = Str::slug($data["title->".$language['locale']]);

                    // Check for duplicate slugs (same language)
                    $count = Content::where('slug->'.$language['locale'], $slug)->count();

                    $slugs[$language['locale']] = $slug;

                    if($count > 0) {
                        $slugs[$language['locale']] = $slug . "-" . rand(1111, 9999);
                    }
                }
            }
        } catch (QueryException | Exception  | \Throwable $e) {
            logError('slug: '.json_encode($e->getMessage()));
        }

        return (object)$slugs;
    }

    /**
     * Create default content sections
     *
     * @param $data
     * @return array
     */
    public function createContentSections($data): array {
        try {
            $configs = HContent::getContentConfigurations($data["type"]);

            return $configs->sections;
        } catch (QueryException | Exception  | \Throwable $e) {
            logError('slug: '.json_encode($e->getMessage()));
        }

        return [];
    }

    private function validateType(string $type): void {
        try {
            $configs = HContent::getContentConfigurations($type);

            // Test if fields and sections exist (even if empty)
            $configs->fields;
            $configs->sections;

            $this->contentType = $type;
        } catch (QueryException | Exception  | \Throwable $e) {
            logError('Invalid content type: '.$type);
            abort(404);
        }
    }

    public static  function getContents($trashed) {
        if($trashed)
            return Content::withTrashed()->get();
        else
            return Content::get();
    }

    public static  function getContentsByPos($trashed) {
        if($trashed)
            return Content::withTrashed()->orderBy('position')->get();
        else
            return Content::orderBy('position')->get();
    }
}
