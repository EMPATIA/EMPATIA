<?php

namespace App\Http\Controllers\Backend\CMS;

use Doctrine\DBAL\Query\QueryException;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\Backend\CMS\Language;
use App\Helpers\HBackend;
use App\Helpers\HForm;
use App\Helpers\HCache;
use Arr;
use App\Http\Controllers\Backend\Controller;

class LanguagesController extends Controller
{
    // Controller prefix
    private $prefix = "backend.cms.languages.";

    // Validation rules and messages
    private $validateRules = [];
    private $validateMessages = [];

    private $guarded = [
        'id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];
    protected $listeners = ['destroy', 'restore'];


        function __construct($id = null)
    {
//        parent::__construct($id);

        [$this->validateRules, $this->validateMessages] = HBackend::createControllerValidate([
            'locale' => [
                'rules' => ['required'],
                'locale' => false,
            ],
            'name' => [
                'rules' => ['required'],
                'locale' => false,
            ],
        ], $this->prefix.'form.error');
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
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
        return view($this->prefix.'language', [
            'language' => [],
            'action' => HForm::$CREATE,
            'title' => __($this->prefix.'create.title'),
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
            // Create record in database
            if($language = Language::create($request->except($this->guarded))){
                DB::commit();
                // Clear languages cache
                HCache::flushLanguages();

                flash()->addSuccess(__('backend.generic.store.ok'));
                return redirect()->action([LanguagesController::class, 'show'], $language->id);
            }

        } catch (QueryException | Exception  | \Throwable $e) {
            DB::rollBack();
            logError($e->getMessage() . ' at line: ' . $e->getLine());
            flash()->addError(__('backend.generic.store.error'));
            return redirect()->back()->withInput();
        }
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        $language = Language::findOrFail($id);

        return view($this->prefix.'language',[
            'language' => $language,
            'action' => HForm::$SHOW,
            'title' => __($this->prefix.'show.title')." :: ".getField($language, 'name'),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        $language = Language::findOrFail($id);

        return view($this->prefix.'language',[
            'language' => $language,
            'action' => HForm::$EDIT,
            'title' => __($this->prefix.'edit.title')." :: ".getField($language, 'name'),
        ]);
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $request->validate($this->validateRules, $this->validateMessages);

        // Handle checkboxes
        $arr = $request->except($this->guarded);
        $arr['default'] = $request->default ?? "0";
        $arr['backend'] = $request->backend ?? "0";
        $arr['frontend'] = $request->frontend ?? "0";

        try {
            DB::beginTransaction();
            if(Language::findOrFail($id)->update($arr)) {
                DB::commit();
                HCache::flushLanguages();

                flash()->addSuccess(__('backend.generic.update.ok'));
                return redirect()->action([LanguagesController::class, 'show'], $id);
            } else {
                logError('error in update');
                flash()->addError(__('backend.generic.update.error'));
                return redirect()->back()->withInput();
            }
        } catch (QueryException | Exception  | \Throwable $e) {
            DB::rollBack();
            logError($e->getMessage() . ' at line: ' . $e->getLine());
            flash()->addError(__('backend.generic.update.error'));
            return redirect()->back()->withInput();
        }
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
            if(Language::findOrFail($id)->delete()) {
                DB::commit();
                HCache::flushLanguages();
                flash()->addSuccess(__('backend.generic.destroy.ok'));
                return response()->json(['success' => 'success'], 200);
            }

            logError('delete error');
            flash()->addError(__('backend.generic.destroy.error'));
        } catch (QueryException | Exception  | \Throwable $e) {
            DB::rollBack();
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
            if (Language::withTrashed()->findOrFail($id)->restore()) {
                DB::commit();
                HCache::flushLanguages();
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
