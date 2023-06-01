<?php

namespace App\Http\Controllers\Backend;

use App\Helpers\HBackend;
use App\Helpers\HForm;
use App\Http\Controllers\Backend\CMS\Exception;
use App\Models\Backend\Configuration;
use Carbon\Carbon;
use Doctrine\DBAL\Query\QueryException;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ConfigurationsController extends Controller
{
    // Controller prefix
    private $prefix = "backend.configurations.";

    // Validation rules and messages
    private $validateRules = [];
    private $validateMessages = [];

    protected $listeners = ['destroy', 'restore'];

    private $guarded = [
        'id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    function __construct($id = null)
    {
//        parent::__construct($id);

        [$this->validateRules, $this->validateMessages] = HBackend::createControllerValidate([
            'code' => [
                'rules' => ['required', 'max:255'],
                'locale' => false,
            ],
            'configurations' => [
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
        return view($this->prefix.'configuration', [
            'configuration' => [],
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
            if ($configuration = Configuration::create($request->except($this->guarded))) {
                DB::commit();
                flash()->addSuccess(__('backend.generic.store.ok'));
                return redirect()->action([ConfigurationsController::class, 'show'], $configuration->id);
            }

        } catch (QueryException|Exception|\Throwable $e) {
            DB::rollBack();
            logError($e->getMessage() . ' at line: ' . $e->getLine());
            flash()->addError(__('backend.generic.destroy.error'));
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
        $configuration = Configuration::findOrFail($id);

        return view($this->prefix.'configuration',[
            'configuration' => $configuration,
            'action' => HForm::$SHOW,
            'title' => __($this->prefix.'show.title')." :: ".getField($configuration, 'code'),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        $configuration = Configuration::findOrFail($id);

        return view($this->prefix.'configuration',[
            'configuration' => $configuration,
            'action' => HForm::$EDIT,
            'title' => __($this->prefix.'edit.title')." :: ".getField($configuration, 'code'),
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
        try {
            $configuration = Configuration::findOrFail($id);
            DB::beginTransaction();
            if($configuration->update([
                'code' => Str::slug($request->code),
                'configurations' => json_decode($request->configurations)
            ])) {
                DB::commit();
                flash()->addSuccess(__('backend.generic.update.ok'));
                return redirect()->action([ConfigurationsController::class, 'show'], $id);
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
            if (Configuration::findOrFail($id)->delete()) {
                DB::commit();
                flash()->addSuccess(__('backend.generic.destroy.ok'));
                return response()->json(['success' => 'success'], 200);
            }

        } catch (QueryException|Exception|\Throwable $e) {
            DB::rollBack();
            logError($e->getMessage() . ' at line: ' . $e->getLine());
            flash()->addError(__('backend.generic.destroy.error'));
            return redirect()->back()->withInput();
        }
    }

    /**
     * Restore the specified resource in storage.
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function restore($id)
    {
        try {
            DB::beginTransaction();
            if (Configuration::withTrashed()->findOrFail($id)->restore()) {
                DB::commit();
                flash()->addSuccess(__('backend.generic.restore.ok'));
                return response()->json(['success' => 'success'], 200);
            }
        } catch (QueryException|Exception|\Throwable $e) {
            DB::rollBack();
            logError($e->getMessage() . ' at line: ' . $e->getLine());
            flash()->addError(__('backend.generic.restore.error'));
            return redirect()->back()->withInput();
        }
    }

}
