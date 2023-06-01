<?php

namespace App\Http\Controllers\Backend\Empatia;

use App\Helpers\HBackend;
use App\Helpers\HDatatable;
use App\Helpers\HForm;
use App\Models\Backend\Notifications\Template;
use Arr;
use Carbon\Carbon;
use Doctrine\DBAL\Query\QueryException;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\Empatia\LoginLevel;
use Illuminate\Support\Facades\Gate;

class LoginLevelsController extends Controller
{
    use AuthorizesRequests;

    // Controller prefix
    private $prefix = "backend.empatia.login-levels.";

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

        [$this->validateRules, $this->validateMessages] = HBackend::createControllerValidate([
            'code' => [
                'rules' => ['required', 'max:255'],
                'locale' => false,
            ],
            'name' => [
                'rules' => ['required', 'max:255'],
                'locale' => true,
            ],
            'dependencies' => [
                'rules' => ['required', 'max:255'],
                'locale' => true,
            ],
            'data' => [
                'rules' => [],
                'locale' => false,
            ],
        ], $this->prefix.'form.error');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authorize('view-any', LoginLevel::class);

        return view($this->prefix.'index', [
            'title' => __($this->prefix.'index.title'),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('create', LoginLevel::class);

        return view($this->prefix.'login-level', [
            'loginLevel' => [],
            'action' => HForm::$CREATE,
            'title' => __($this->prefix.'create.title'),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->authorize('create', LoginLevel::class);

        $request->validate($this->validateRules, $this->validateMessages);

        try {
            DB::beginTransaction();
            if($loginLevel = LoginLevel::create([
                'code' => $request->input('code'),
                'name' => HBackend::setInput($request, 'name'),
                'dependencies' => HBackend::setInput($request, 'dependencies'),
            ])){
                DB::commit();
                logDebug('Store: ' . json_encode($loginLevel));
                flash()->addSuccess(__('backend.generic.store.ok'));
                return redirect()->action([LoginLevelsController::class, 'show'], $loginLevel->id);
            }
        } catch (QueryException | Exception  | \Throwable $e) {
            DB::rollback();
            logError('store: '.json_encode($e->getMessage()));
            flash()->addError(__('backend.generic.store.error'));
            return redirect()->back()->withInput();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\LoginLevel  $loginLevel
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $loginLevel = LoginLevel::whereId($id)->firstOrFail();

        $this->authorize('view', $loginLevel);

        return view($this->prefix.'login-level',[
            'loginLevel' => $loginLevel,
            'action' => HForm::$SHOW,
            'title' => __($this->prefix.'show.title'),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\LoginLevel  $loginLevel
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $loginLevel = LoginLevel::whereId($id)->firstOrFail();

       $this->authorize('update', $loginLevel);

        return view($this->prefix.'login-level',[
            'loginLevel' => $loginLevel,
            'action' => HForm::$EDIT,
            'title' => __($this->prefix.'edit.title'),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\LoginLevel  $loginLevel
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate($this->validateRules, $this->validateMessages);

        $loginLevel = LoginLevel::findOrFail($id);

        $this->authorize('update', $loginLevel);

        try {
            DB::beginTransaction();
            if($loginLevel->update([
                'code' => $request->input('code'),
                'name' => HBackend::setInput($request, 'name'),
                'dependencies' => HBackend::setInput($request, 'dependencies'),

            ])){
                DB::commit();
                logDebug('Update: ' . json_encode($loginLevel));
                flash()->addSuccess(__('backend.generic.update.ok'));
                return redirect()->action([LoginLevelsController::class, 'show'], $loginLevel->id);
            }
        } catch (QueryException | Exception  | \Throwable $e) {
            DB::rollback();
            logError('update: '.json_encode($e->getMessage()));
            flash()->addError(__('backend.generic.update.error'));
            return redirect()->back()->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\LoginLevel  $loginLevel
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $loginLevel = LoginLevel::findOrFail($id);

        $this->authorize('delete', $loginLevel);

        try {
            DB::beginTransaction();
            if($loginLevel->delete()) {
                DB::commit();
                flash()->addSuccess(__('backend.generic.destroy.ok'));
                return response()->json(['success' => 'success'], 200);
            }

            logError('delete error');
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
        $loginLevel = LoginLevel::withTrashed()->findOrFail($id);

        $this->authorize('restore', $loginLevel);

        try {
            DB::beginTransaction();
            if ($loginLevel->restore()) {
                DB::commit();
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
