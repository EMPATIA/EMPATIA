<?php

namespace App\Http\Controllers\Backend\Notifications;

use App\Helpers\HBackend;
use App\Helpers\HForm;
use App\Http\Controllers\Backend\Controller;
use App\Models\Backend\Notifications\Template;
use Arr;
use Doctrine\DBAL\Query\QueryException;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TemplatesController extends Controller
{
    use AuthorizesRequests;

    // Controller prefix
    private $prefix = "backend.notifications.templates.";

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
            'code' => [
                'rules' => ['required', 'max:255'],
                'locale' => false,
            ],
            'channel' => [
                'rules' => ['required', 'max:255'],
                'locale' => false,
            ],
            'subject' => [
                'rules' => ['required', 'max:255'],
                'locale' => true,
            ],
            'content' => [
                'rules' => ['required'],
                'locale' => true,
            ],
        ], $this->prefix.'form.error');
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        $this->authorize('view-any', Template::class);

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
        $this->authorize('create', Template::class);

        return view($this->prefix.'template', [
            'template' => [],
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
        $this->authorize('create', Template::class);

        $request->validate($this->validateRules, $this->validateMessages);

        try {
            DB::beginTransaction();
            if($template = Template::create([
                'code' => $request->input('code'),
                'channel' => $request->input('channel'),
                'subject' => HBackend::setInput($request, 'subject'),
                'content' => HBackend::setInput($request, 'content'),


            ])){
                DB::commit();
                logDebug('Store: ' . json_encode($template));
                flash()->addSuccess(__('backend.generic.store.ok'));
                return redirect()->action([TemplatesController::class, 'show'], $template->id);
            }
        } catch (QueryException | Exception  | \Throwable $e) {
            DB::rollback();
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
    public function show($id)
    {
        $template = Template::whereId($id)->firstOrFail();

        $this->authorize('view', $template);

        return view($this->prefix.'template',[
            'template' => $template,
            'action' => HForm::$SHOW,
            'title' => __($this->prefix.'show.title'),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        $template = Template::whereId($id)->firstOrFail();

        $this->authorize('update', $template);

        return view($this->prefix.'template',[
            'template' => $template,
            'action' => HForm::$EDIT,
            'title' => __($this->prefix.'edit.title'),
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

        $template = Template::findOrFail($id);

        $this->authorize('update', $template);


        try {

            DB::beginTransaction();
            if($template->update([
                'code' => $request->code,
                'channel' => $request->channel,
                'subject' => HBackend::setInput($request, 'subject'),
                'content' => HBackend::setInput($request, 'content'),

            ])){
                DB::commit();
                logDebug('Update: ' . json_encode($template));
                flash()->addSuccess(__('backend.generic.update.ok'));
                return redirect()->action([TemplatesController::class, 'show'], $template->id);
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
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $template = Template::findOrFail($id);

        $this->authorize('delete', $template);

        try {
            DB::beginTransaction();
            if($template->delete()) {
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
        $template = Template::withTrashed()->findOrFail($id);

        $this->authorize('restore', $template);

        try {
            DB::beginTransaction();
            if ($template->restore()) {
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
