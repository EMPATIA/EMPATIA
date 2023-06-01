<?php

namespace App\Http\Controllers\Backend\CMS;

use Doctrine\DBAL\Query\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use App\Helpers\HBackend;
use App\Helpers\HForm;
use App\Helpers\HCache;
use App\Models\Backend\CMS\Translation;
use Pam\CookieConsent\Http\Controllers\Controller;


class TranslationsController extends Controller
{
    // Controller prefix
    private $prefix = "backend.cms.translations.";

    // Validation rules and messages
    private $validateRules = [];
    private $validateMessages = [];

    private $guarded = [
        'id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];
    
    
    function __construct()
    {
        [$this->validateRules, $this->validateMessages] = HBackend::createControllerValidate([
            'locale' => [
                'rules' => ['string'],
                'locale' => false,
            ],
            'namespace' => [
                'rules' => ['required','string'],
                'locale' => false,
            ],
            'group' => [
                'rules' => ['required','string'],
                'locale' => false,
            ],
            'item' => [
                'rules' => ['required','string'],
                'locale' => false,
            ],
            'text' => [
                'rules' => ['required','string'],
                'locale' => true,
            ],
        ], $this->prefix.'form.error');
    }
    
    /**
     * Display a listing of the resource.
     * @return View
     */
    public function index() : View
    {
        return view($this->prefix.'index', [
            'title' => __($this->prefix.'index.title'),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view($this->prefix. 'translation', [
            'translation' => [],
            'action' => HForm::$CREATE,
            'title' => __($this->prefix.'create.title')
        ]);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate($this->validateRules, $this->validateMessages);

        try {
            DB::beginTransaction();
            foreach (getLanguagesFrontend() ?? [] as $language) {   // Just FE languages because it's what is used at the input-lang input
                Translation::create([
                    'locale' =>  $language['locale'],
                    'namespace' => Str::slug($request->input('namespace')),
                    'group' => Str::slug($request->input('group')),
                    'item' => Str::slug($request->input('item')),
                    'text' => getField(HBackend::setInput($request, 'text'), $language['locale'])
                ]);
            }
            DB::commit();

            flash()->addSuccess(__('backend.generic.store.ok'));
            return redirect()->action([self::class, 'index']);

        } catch (QueryException|Exception|\Throwable $e) {
            DB::rollback();
            logError($e->getMessage() . ' at line: ' . $e->getLine());
        }
        flash()->addError(__('backend.generic.store.error'));
        return redirect()->back()->withInput();
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            if(Translation::findOrFail($id)->delete()) {
                DB::commit();
                HCache::flushTranslationId($id);
                flash()->addSuccess(__('backend.generic.destroy.ok'));
                return response()->json(['success' => 'success'], 200);
            }
            logError('Delete Error');

        } catch (QueryException | Exception  | \Throwable $e) {
            logError($e->getMessage() . ' at line: ' . $e->getLine());
            flash()->addError(__('backend.generic.destroy.error'));
            return redirect()->back()->withInput();
        }
        flash()->addError(__('backend.generic.destroy.error'));
        return redirect()->back();
    }

    /**
     * Restore the specified resource from storage.
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function restore($id)
    {
        try {
            DB::beginTransaction();
            if (Translation::withTrashed()->findOrFail($id)->restore()) {
                DB::commit();
                HCache::flushTranslationId($id);
                flash()->addSuccess(__('backend.generic.restore.ok'));
                return response()->json(['success' => 'success'], 200);
            }

        } catch (QueryException | Exception | \Throwable $e) {
            DB::rollBack();
            logError($e->getMessage() . ' at line: ' . $e->getLine());
            flash()->addError(__('backend.generic.restore.error'));
            return redirect()->back()->withInput();
        }
        flash()->addError(__('backend.generic.restore.error'));
        return redirect()->back();
    }
}
