<?php

namespace App\Http\Controllers\Backend\Empatia\Cbs;

use App\Helpers\HBackend;
use App\Helpers\HForm;
use App\Http\Controllers\Backend\Controller;
use App\Helpers\Empatia\Cbs\HCb;
use App\Models\Empatia\Cbs\Cb;
use App\Models\Empatia\Cbs\TechnicalAnalysisQuestion;
use Closure;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Throwable;

class TechnicalAnalysisController extends Controller
{
    private string $prefix = "backend.empatia.cbs.default.technical-analysis.";
    
    private array $validateRules = [];
    private array $validateMessages = [];
    
    public ?CB $cb;
    public ?TechnicalAnalysisQuestion $technicalAnalysisQuestion;
    
    protected array $listeners = [
        'destroy'
    ];
    
    function __construct()
    {
        [$this->validateRules, $this->validateMessages] = HBackend::createControllerValidate([
            'code' => [
                'rules' => ['required'],
            ],
            'type' => [
                'rules' => ['required'],
            ],
            'value' => [
                'rules' => ['required', 'max:255'],
                'locale' => true
            ]
        ], $this->prefix . 'form.error');
    }
    
    /**
     * Display a listing of the resource.
     * @param string $cbType
     * @param string $cbId
     * @return View|RedirectResponse
     */
    public function index(string $cbType, string $cbId): View|RedirectResponse
    {
        $this->authorize('view-any', TechnicalAnalysisQuestion::class);
        
        if ($cbType != 'all')
            $cbType = HCb::validateType($cbType);
        
        $this->cb = Cb::find($cbId);
        
        if (checkEmpty($cbType, $this->cb)) {
            flash()->addError(__('backend.generic.error'));
            return redirect()->back()->withInput();
        }
        
        return view($this->prefix . 'index', [
            'cb' => $this->cb,
            'title' => __($this->prefix . 'index.title'),
        ]);
    }
    
    /**
     * Show the specified resource.
     * @param string $cbType
     * @param string $cbId
     * @param string $code
     * @return View|RedirectResponse
     * @throws AuthorizationException
     */
    public function show(string $cbType, string $cbId, string $code): View|RedirectResponse
    {
        if (!$this->commonValidations($cbType, $cbId, $code))
            return redirect()->back()->withInput();
        
        $this->authorize('view', $this->technicalAnalysisQuestion);
        
        return view($this->prefix . 'question', [
            'question' => $this->technicalAnalysisQuestion,
            'title' => __($this->prefix . 'show.title'),
            'cb' => $this->cb,
            'questionTypeOptions' => $this->cb->technicalAnalysisQuestionTypes(true)
        ]);
        
    }
    
    /**
     * Show the form for creating a new resource.
     * @param string $cbType
     * @param string $cbId
     * @return View|RedirectResponse
     * @throws AuthorizationException
     */
    public function create(string $cbType, string $cbId): View|RedirectResponse
    {
        $this->authorize('create', TechnicalAnalysisQuestion::class);
        
        if ($cbType != 'all')
            $cbType = HCb::validateType($cbType);
        
        $this->cb = Cb::find($cbId);
        
        if (checkEmpty($cbType, $this->cb)) {
            flash()->addError(__('backend.generic.error'));
            return redirect()->back()->withInput();
        }
        
        return view($this->prefix . 'question', [
            'question' => [],
            'title' => __($this->prefix . 'create.title'),
            'cb' => $this->cb,
            'questionTypeOptions' => $this->cb->technicalAnalysisQuestionTypes(true)
        ]);
        
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @param string $cbType
     * @param string $cbId
     * @return RedirectResponse|void
     * @throws AuthorizationException
     */
    public function store(Request $request, string $cbType, string $cbId)
    {
        $this->authorize('create', TechnicalAnalysisQuestion::class);
        
        if ($cbType != 'all')
            $cbType = HCb::validateType($cbType);
        
        $this->cb = Cb::find($cbId);
        
        if (checkEmpty($cbType, $this->cb)) {
            flash()->addError(__('backend.generic.error'));
            return redirect()->back()->withInput();
        }
        
        self::addCodeUniqueValidation(Str::slug($request->input('code')));
        
        $validator = Validator::make($request->all(), $this->validateRules, $this->validateMessages);
        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator->errors());
        }
        
        try {
            $newQuestion = $this->formatNewQuestion($request);
            
            DB::beginTransaction();
            TechnicalAnalysisQuestion::linkModel($this->cb);
            if (TechnicalAnalysisQuestion::create((array)$newQuestion)) {
                DB::commit();
                flash()->addSuccess(__('backend.generic.store.ok'));
                return redirect()->action([TechnicalAnalysisController::class, 'show'], ['type' => $this->cb->type ?? 'all', 'cbId' => $this->cb->id, 'code' => $newQuestion->code]);
            }
            
        } catch (QueryException|Exception|Throwable $e) {
            DB::rollback();
            logError($e->getMessage());
        }
        
        flash()->addError(__('backend.generic.store.error'));
        return redirect()->back()->withInput();
    }
    
    /**
     * Edit the specified resource.
     * @param string $cbType
     * @param string $cbId
     * @param string $code
     * @return View|RedirectResponse
     * @throws AuthorizationException
     */
    public function edit(string $cbType, string $cbId, string $code): View|RedirectResponse
    {
        if (!$this->commonValidations($cbType, $cbId, $code))
            return redirect()->back()->withInput();
        
        $this->authorize('update', $this->technicalAnalysisQuestion);
        
        return view($this->prefix . 'question', [
            'question' => $this->technicalAnalysisQuestion,
            'title' => __($this->prefix . 'show.title'),
            'cb' => $this->cb,
            'questionTypeOptions' => $this->cb->technicalAnalysisQuestionTypes(true)
        ]);
        
    }
    
    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param string $cbType
     * @param string $cbId
     * @param string $code
     * @return RedirectResponse|void
     * @throws AuthorizationException
     */
    public function update(Request $request, string $cbType, string $cbId, string $code)
    {
        if (!$this->commonValidations($cbType, $cbId, $code))
            return redirect()->back()->withInput();
        
        $this->authorize('update', $this->technicalAnalysisQuestion);
        
        self::addCodeUniqueValidation(Str::slug($request->input('code')), $this->technicalAnalysisQuestion);
        
        $validator = Validator::make($request->all(), $this->validateRules, $this->validateMessages);
        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator->errors());
        }
        
        try {
            $editedQuestion = $this->formatNewQuestion($request);
            
            DB::beginTransaction();
            if ($this->technicalAnalysisQuestion->update((array)$editedQuestion)) {
                DB::commit();
                flash()->addSuccess(__('backend.generic.update.ok'));
                return redirect()->action([TechnicalAnalysisController::class, 'show'], ['type' => $this->cb->type ?? 'all', 'cbId' => $this->cb->id, 'code' => $editedQuestion->code]);
            }
            
        } catch (QueryException|Exception|Throwable $e) {
            DB::rollback();
            logError($e->getMessage());
        }
        flash()->addError(__('backend.generic.update.error'));
        return redirect()->back()->withInput();
    }
    
    /**
     * Remove the specified resource from storage.
     * @param string $cbType
     * @param string $cbId
     * @param string $code
     * @return RedirectResponse
     * @throws AuthorizationException
     */
    public function destroy(string $cbType, string $cbId, string $code): RedirectResponse
    {
        if (!$this->commonValidations($cbType, $cbId, $code))
            return redirect()->back()->withInput();
        
        $this->authorize('delete', $this->technicalAnalysisQuestion);
        
        try {
            DB::beginTransaction();
            if ($this->technicalAnalysisQuestion->delete()) {
                DB::commit();
                flash()->addSuccess(__('backend.generic.delete.ok'));
                return redirect()->back()->withInput();
            }
            
        } catch (QueryException|Exception|Throwable $e) {
            DB::rollback();
            logError($e->getMessage());
        }
        flash()->addError(__('backend.generic.destroy.error'));
        return redirect()->back()->withInput();
    }
    
    /**
     * Restore the specified resource from storage.
     * @param string $cbType
     * @param string $cbId
     * @param string $code
     * @return RedirectResponse
     * @throws AuthorizationException
     */
    public function restore(string $cbType, string $cbId, string $code): RedirectResponse
    {
        if (!$this->commonValidations($cbType, $cbId, $code))
            return redirect()->back()->withInput();
        
        $this->authorize('restore', $this->technicalAnalysisQuestion);
        
        try {
            DB::beginTransaction();
            if ($this->technicalAnalysisQuestion->restore()) {
                DB::commit();
                flash()->addSuccess(__('backend.generic.restore.ok'));
                return redirect()->back()->withInput();
            }
            
        } catch (QueryException|Exception|Throwable $e) {
            DB::rollback();
            logError($e->getMessage());
        }
        flash()->addError(__('backend.generic.destroy.error'));
        return redirect()->back()->withInput();
    }
    
    /**
     * Format new technical analysis question
     * @param Request $request
     * @return object
     */
    public function formatNewQuestion(Request $request): object
    {
        return (object)[
            'code' => Str::slug($request->input('code')),
            'type' => $request->input('type'),
            'value' => HBackend::setInput($request, 'value'),
            'enabled' => (bool)$request->input('enabled')
        ];
    }
    
    /**
     * Add the unique validation to "code" field
     * @param string $questionCode
     * @param TechnicalAnalysisQuestion|null $question
     * @return void
     */
    public function addCodeUniqueValidation(string $questionCode, TechnicalAnalysisQuestion $question = null): void
    {
        $this->validateRules['code'][] =
            function (string $attribute, mixed $value, $fail) use ($questionCode, $question) {
                TechnicalAnalysisQuestion::linkModel($this->cb);
                
                if (empty($question) && !TechnicalAnalysisQuestion::withTrashed()->whereCode($questionCode)->get()->isEmpty())
                    $fail(__($this->prefix . 'form.error.code.unique'));
                
                if (!empty($question) && !TechnicalAnalysisQuestion::withTrashed()->whereCode($questionCode)->where('code', '!=', $question->code)->get()->isEmpty())
                    $fail(__($this->prefix . 'form.error.code.unique'));
            };
    }
    
    /**
     * Do all common validations for the controller principal methods
     * @param string $cbType
     * @param string $cbId
     * @param string $code
     * @return bool
     */
    public function commonValidations(string $cbType, string $cbId, string $code): bool
    {
        if ($cbType != 'all')
            $type = HCb::validateType($cbType);
        
        $this->cb = Cb::find($cbId);
        
        TechnicalAnalysisQuestion::linkModel($this->cb);
        $this->technicalAnalysisQuestion = TechnicalAnalysisQuestion::withTrashed()->find($code);
        
        if (checkEmpty($type, $this->cb, $this->technicalAnalysisQuestion)) {
            logError('Couldn\'t find a necessary property.');
            flash()->addError(__('backend.generic.error'));
            return false;
        }
        return true;
    }
    
}
