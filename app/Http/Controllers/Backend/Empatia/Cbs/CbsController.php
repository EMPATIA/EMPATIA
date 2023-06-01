<?php

namespace App\Http\Controllers\Backend\Empatia\Cbs;

use App\Helpers\HBackend;
use App\Helpers\HForm;
use App\Http\Controllers\Backend\Controller;
use Carbon\Carbon;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validate\ValidationException;
use Modules\Cbs\Http\Controllers\Exception;
use App\Models\Empatia\Cbs\Cb;
use App\Helpers\Empatia\Cbs\HCb;
use Modules\Frontend\Helpers\CvHelpers;
use Illuminate\Support\Str;


class CbsController extends Controller
{
    private $prefix = "backend.empatia.cbs.";

    public $cbType;

    private $validateRules = [];
    private $validateMessages = [];

    private $guarded = [
        'site',
        'id',
        'updated_by',
        'deleted_by',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    private $settings = null;

    public $typeFilter = null;

    /**
     * List of methods to be authorized with permissions/roles
     *
     * @var string[]
     */
    private array $guardedMethods = [
        'index',
        'create',
        'store',
        'show',
        'edit',
        'update',
        'destroy',
    ];

    // Listeners
    protected $listeners = ['destroy', 'restore', 'indexFilters', 'resetIndexFilters', 'filterCbType'];


    // TODO:
    //  - refactor these permissions
    private array $permissions = [
        'create' => [
            'create-cb',
        ],
        'index' => [
            'access-cbs-index',
        ],
        'show' => [
            'access-cb-show',
        ],
        'delete' => [
            'delete-cb',
        ]
    ];


    function __construct($id = null)
    {
//        parent::__construct($id);

        [$this->validateRulesEdit, $this->validateMessages] = HBackend::createControllerValidate([
            'title' => [
                'rules' => ['required', 'max:50'],
                'locale' => true
            ],
            'start_date' => [
                'rules' => ['required', 'date'],
            ],
            'end_date' => [
                'rules' => ['required', 'date', 'after_or_equal:start_date'],
            ],
            'type' => [
                'rules' => ['required'],
            ],
            'template' => [
                'rules' => ['max:50'],
            ],
            'code' => [
                'rules' => ['required'],
            ],
            'content' => [
                'rules' => ['string'],
            ],
            'slug' => [
                'rules' => ['max:100'],
                'locale' => true
            ],
            'data' => [
                'rules' => [],
            ]
        ], $this->prefix . 'form.error');
    }

    /**
     * Performs permissions/roles authorization checks on the called method.
     * @param string|null $method The method to be checked.
     * @return bool
     */
    private function authorizationCheck(string $method = null): bool
    {
        try {
            $method = $method ?? get_called_method(1);

            if (!in_array($method, $this->guardedMethods)) {
                return true;
            }

            $user = auth()->user();
            if ($user->hasAnyRole(['admin', 'laravel-admin']) || $user->canAny($this->permissions[$method] ?? [])) {
                return true;
            }

        } catch (\Exception $e) {
            logError($e->getMessage() . ' at line ' . $e->getLine());
        }

        // TODO:
        //  - get 'permissions' http status code from config
        abort(403);
        return false;
    }


    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index($type = 'all')
    {
        $this->authorizationCheck();

        if ($type != 'all')
            $this->cbType = HCb::validateType($type);

        $view = "backend.empatia.cbs.$type.index";
        if (!view()->exists($view)) {
            $view = "backend.empatia.cbs.default.index";
        }

        //        $indexFilters = HBackend::initializeIndexFilters();

        return view($view, compact('type'), [
            'title' => __($this->prefix.'title.index'),
//            'deletionStatusFilter' => $indexFilters['deletionStatusFilter'],
//            'deletionStatusFilterOptions' => $indexFilters['deletionStatusFilterOptions'],
            'cbType' => HCb::getCbTypes()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create($type)
    {
        $this->authorizationCheck();

        if ($type != 'all') {
            $this->cbType = HCb::validateType($type);
        }

        $view = "backend.empatia.cbs.$type.cb";
        if (!view()->exists($view)) {
            $view = "backend.empatia.cbs.default.cb";
        }

        $cb = [];
        if ($type != 'all') {
            $cb['type'] = $type;
        }
//        dd($view);
        return view($view, [
            'title'     => __($this->prefix.'title.create'),
            'model'     => $cb,
            'type'      => $type,
            'cbTypes'   => HCb::getCbTypes(),
            'action'    => HForm::$CREATE,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request, $type)
    {
        logDebug('init');

        $this->authorizationCheck();

        logDebug('type -> ' . $type);

        if ($type != 'all') {
            HCb::validateType($type);
        }

        $request->validate($this->validateRulesEdit, $this->validateMessages);

        try {
            $this->settings = $this->settings ?? HBackend::getConfigurationByCode('cb_settings');
            DB::beginTransaction();
            $cb = Cb::create([
                'type' => $request->input('type'),
                'template' => $request->input('template') ?? 'default',
                'code' => HCb::setCbCode($request, 'store'),
//                'code' => Str::slug($request->input("code")),
                'title' => HBackend::setInput($request->all(), 'title'),
                'start_date' => $request->input('start_date'),
                'end_date' => $request->input('end_date'),
                'content' => HBackend::setInput($request->all(), 'content'),
                'slug' => HCb::setCbSlug($request),
                'parameters' => getField($this->settings, "types.".$request->input('type').".parameters", []),
                'data' => $request->input('data')
            ]);

            \Cache::forget('cache_cbs_' . $type);

            flash()->addSuccess(__('backend.generic.store.ok'));

            DB::commit();

            logDebug('created cb ' . $cb->id);
            logDebug('finish');

            return redirect()->action([self::class, 'show'], ['type' => $cb->type ?? 'all', 'id' => $cb->id]);

        } catch (QueryException|Exception|\Throwable $e) {
            DB::rollback();
            logError($e->getMessage() .' at line '. $e->getLine());
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
        $this->authorizationCheck();

        if ($type != 'all')
            $this->cbType = HCb::validateType($type);

        $view = "backend.empatia.cbs.$type.cb";
        if (!view()->exists($view)) {
            $view = "backend.empatia.cbs.default.cb";
        }

        $this->cb = Cb::findOrFail($id);
        $this->cb->dates = Carbon::parse($this->cb->start_date)->isoFormat('Y-MM-DD') . ' | ' . Carbon::parse($this->cb->end_date)->isoFormat('Y-MM-DD');

        return view($view, [
            'title' => __($this->prefix.'title.show'),
            'type' => $type,
            'model' => $this->cb,
            'cbTypes' => HCb::getCbTypes(),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($type, $id)
    {
        $this->authorizationCheck();

        if ($type != 'all')
            $this->cbType = HCb::validateType($type);

        $view = "backend.empatia.cbs.$type.cb";
        if (!view()->exists($view)) {
            $view = "backend.empatia.cbs.default.cb";
        }

        $this->cb = Cb::findOrFail($id);

        return view($view, [
            'title' => __($this->prefix.'title.edit'),
            'type' => $type,
            'model' => $this->cb,
            'cbTypes' => HCb::getCbTypes()
        ]);
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $type, $id)
    {
        logDebug('init');

        $this->authorizationCheck();

        logDebug('type -> ' . $type);

        if ($type != 'all')
            HCb::validateType($type);

        $request->validate($this->validateRulesEdit, $this->validateMessages);
        try {
            DB::beginTransaction();

            $cb = Cb::find($id);
            self::saveNewVersion($cb->id);

            $cb->title = HBackend::setInput($request->all(), 'title');
            $cb->type = $request->input('type') ?? $cb->type;
            $cb->template = $request->input('template') ?? $cb->template;
            $cb->code = HCb::setCbCode($request, 'update', $cb->id);
            $cb->start_date = $request->input('start_date');
            $cb->end_date = $request->input('end_date');
            $cb->slug = HBackend::setInput($request->all(), 'slug');
            $cb->content = HBackend::setInput($request->all(), 'content');
            $cb->data = json_decode($request->input('data'));
            //TODO: Adicionar Trait Versionable

            if ($cb->save()) {
                flash()->addSuccess(__('backend.generic.update.ok'));
                DB::commit();
                logDebug('updated cb ' . $cb->id);
                logDebug('finish');
                return redirect()->action([CbsController::class, 'show'], ['type' => $cb->type ?? 'all', 'id' => $id]);

            } else {
                logError('error on update of cb ' . $id);
                DB::rollback();
                flash()->addError(__('backend.generic.update.error'));
                return redirect()->back()->withInput();
            }
        } catch (QueryException|Exception|\Throwable $e) {
            DB::rollback();
            logError($e->getMessage() . ' at line ' . $e->getLine());
            flash()->addError(__('backend.generic.update.error'));
            return redirect()->back()->withInput();
        }
    }
    
    /**
     * Remove the specified resource from storage.
     * @param string $type
     * @param string $id
     * @return RedirectResponse
     */
    public function destroy(string $type, string $id)
    {
        try {
            DB::beginTransaction();
            if (Cb::findOrFail($id)->delete()) {
                DB::commit();
                flash()->addSuccess(__('backend.generic.destroy.ok'));
                return response()->json(['success' => 'success'], 200);
            }
        } catch (QueryException|Exception|\Throwable $e) {
            DB::rollBack();
            logError($e->getMessage() . ' at line ' . $e->getLine());
            flash()->addError(__('backend.generic.destroy.error'));
            return redirect()->back();
        }
    }

    /**
     * Restore the specified resource from storage.
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function restore(string $type, string $id)
    {
        try {
            DB::beginTransaction();
            if (Cb::withTrashed()->findOrFail($id)->restore()) {
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

    public function indexFilters($deletionStatusFilter, $startDateFilter, $endDateFilter)
    {
        $this->deletionStatusFilter = $deletionStatusFilter;
        $this->startDateFilter = $startDateFilter;
        $this->endDateFilter = $endDateFilter;
    }

    public function resetIndexFilters()
    {
        $this->deletionStatusFilter = false;
        $this->startDateFilter = [];
        $this->endDateFilter = [];
    }

    public static function saveNewVersion($id)
    {
        try {
            $cb = Cb::findOrFail($id);
            $versions = (array)$cb->versions;
            $pos = count($versions);
            $version = $cb->version + 1;
            $versions[$pos] = (object)[
                'version' => $cb->version,
                'user' => \Auth::id(),
                'date' => Carbon::now(),
                'type' => $cb->type,
                'template' => $cb->template,
                'code' => $cb->code,
                'title' => $cb->title,
                'start_date' => $cb->start_date,
                'end_date' => $cb->end_date,
                'content' => $cb->content,
                'slug' => $cb->slug,
                'parameters' => $cb->parameters,
                'data' => $cb->data,
                'votes' => $cb->votes
            ];
            DB::beginTransaction();
            if ($cb->update(['version' => $version, 'versions' => $versions])) {
                DB::commit();
                return $cb;
            }
        } catch (\Exception $e) {
            DB::rollback();
            logError($e->getMessage() . ' at line ' . $e->getLine());
            return redirect()->back()->withFail(__('cbs::cb-parameters.delete.error'));
        }
    }

    public function statistics($type, $id)
    {
        $this->authorizationCheck();

        if ($type != 'all')
            HCb::validateType($type);

        $view = "backend.empatia.cbs.$type.statistics";
        if (!view()->exists($view)) {
            $view = "backend.empatia.cbs.default.statistics";
        }

        $cb = Cb::findOrFail($id);
        return view($view, [
            'type' => $type,
            'model' => $cb
        ]);
    }

    public function parameters($type, $id)
    {
        $this->authorizationCheck();

        if ($type != 'all')
            HCb::validateType($type);

        $view = "backend.empatia.cbs.default.parameters";

        $cb = Cb::findOrFail($id);

        $cb->dates = Carbon::parse($cb->start_date)->isoFormat('Y-MM-DD') . ' | ' . Carbon::parse($cb->end_date)->isoFormat('Y-MM-DD');
        return view($view, ['model' => $cb]);
    }

    public static function getCbParametersByVersion($cb, $version)
    {
        if ($version == $cb->version)
            return $cb->parameters;
        foreach ($cb->versions as $v) {
            if ($v->version == $version)
                return $v->parameters;
        }
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function createEmpaville()
    {
        $this->authorizationCheck();

        $view = "empatia::empaville.wizards.create";
        $cb = [];

        return view($view, [
            'model' => $cb,
            'type' => 'empaville',
            'cbTypes' => HCb::getCbTypes(),
            'actionType' => 'create'
        ]);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function storeEmpaville(Request $request, $type = 'empaville')
    {
        logDebug('init');

        $this->authorizationCheck();

        logDebug('type -> ' . $type);

        if ($type != 'all')
            HCb::validateType($type);


        try {
            foreach ($this->validateRulesEdit ?? [] as $key => $rules) {
                if (stripos($key, 'template', 0) !== false) {
                    unset($this->validateRulesEdit[$key]);
                }
                if (stripos($key, 'type', 0) !== false) {
                    unset($this->validateRulesEdit[$key]);
                }
                if (stripos($key, 'code', 0) !== false) {
                    unset($this->validateRulesEdit[$key]);
                }
                if (stripos($key, 'data', 0) !== false) {
                    unset($this->validateRulesEdit[$key]);
                }

            }

            $this->settings = $this->settings ?? HBackend::getConfigurationByCode('cb_settings');
            $configurations = getField($this->settings, 'types.empaville.configurations');
            $request->merge(['type' => 'empaville']);
            $request->merge(['template' => null]);
            $request->merge(['data' => ['configurations' => $configurations]]);
            $request->merge(['code' => $request->input('title->'.getLang())]);

            try{
                $response = $this->store($request, 'empaville');
            } catch(ValidationException|Exception|\Throwable $e) {
                if( $validator = $e->validator ?? null ){
                    if ($validator->fails()) {
                        return redirect()->back()->withErrors($validator->errors())->withInput();
                    }
                } else {
                    throw $e;
                }
            }

            if ($response->getStatusCode() != 302) {
                return redirect()->back()->withFail(__($this->prefix . 'store.error'))->withInput();
            } else {
                return redirect($response->getTargetUrl());
            }

            // catch validation errors thrown inside store()
        } catch (QueryException|Exception|\Throwable $e) {
            DB::rollback();
            logError($e->getMessage() . ' at line ' . $e->getLine());
            return redirect()->back()->withFail(__($this->prefix . 'store.error'))->withInput();
        }
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function showEmpaville($type, $id)
    {
        $this->authorizationCheck();

        if ($type != 'all')
            $this->cbType = HCb::validateType($type);

        $view = "backend.empatia.cbs.$type.cb";

        if (!view()->exists($view)) {
            $view = "backend.empatia.cbs.default.cb";
        }


        $cb = Cb::findOrFail($id);
        $cb->dates = Carbon::parse($cb->start_date)->isoFormat('Y-MM-DD') . ' | ' . Carbon::parse($cb->end_date)->isoFormat('Y-MM-DD');

//        $indexFilters = HBackend::initializeIndexFilters();

        return view($view, [
            'type' => $type,
            'model' => $cb,
            'cbTypes' => HCb::getCbTypes(),
//            'deletionStatusFilter' => $indexFilters['deletionStatusFilter'],
//            'deletionStatusFilterOptions' => $indexFilters['deletionStatusFilterOptions'],
        ]);
    }
}
