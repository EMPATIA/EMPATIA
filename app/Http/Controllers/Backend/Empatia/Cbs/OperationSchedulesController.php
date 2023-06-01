<?php

namespace App\Http\Controllers\Backend\Empatia\Cbs;

use App\Helpers\Empatia\Cbs\HCb;
use App\Helpers\HBackend;
use App\Helpers\HForm;
use App\Models\Empatia\Cbs\OperationSchedule;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\Empatia\Cbs\Cb;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class OperationSchedulesController
{

    use AuthorizesRequests;

    private $prefix = "backend.empatia.cbs.default.operation-schedules";

    public $cbType;

    private $validateRules = [];
    private $validateMessages = [];

    function __construct()
    {
        [$this->validateRules, $this->validateMessages] = HBackend::createControllerValidate([
            'code' => [
                'rules' => ['required', 'unique', 'max: 50'],
            ],
            'description' => [
                'rules' => ['required', 'max:255'],
            ],
            'start_date' => [
                'rules' => ['nullable', 'date'],
            ],
            'end_date' => [
                'rules' => ['nullable', 'date', 'after_or_equal:start_date'],
            ],
            'enabled' => [
                'rules' => ['required'],
            ]
        ], $this->prefix . 'form.error');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($type, $cbId)
    {
        $this->authorize('view-any', OperationSchedule::class);

        if ($type != 'all') {
            $this->cbType = HCb::validateType($type);
        }

        $view = "backend.empatia.cbs.$type.operation-schedules.index";

        if (!view()->exists($view)) {
            $view = "backend.empatia.cbs.default.operation-schedules.index";
        }

        return view($view, [
            'title' => __($this->prefix.'index.title'),
            'type' => $type,
            'cbId' => $cbId,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($type, $cbId)
    {
        $this->authorize('create', OperationSchedule::class);

        if ($type != 'all') {
            $this->cbType = HCb::validateType($type);
        }

        $view = "backend.empatia.cbs.$type.operation-schedules.operation-schedule";

        if (!view()->exists($view)) {
            $view = "backend.empatia.cbs.default.operation-schedules.operation-schedule";
        }

        return view($view, [
            'model' => [],
            'action' => HForm::$CREATE,
            'title' => __($this->prefix.'create.title'),
            'type' => $type,
            'cbId' => $cbId,
        ]);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $type, $cbId)
    {
        $this->authorize('create', OperationSchedule::class);

        if ($type != 'all') {
            HCb::validateType($type);
        }

        $cb = Cb::findOrfail($cbId);

        OperationSchedule::linkModel($cb);

        $validator = Validator::make($request->all(), $this->validateRules, $this->validateMessages);
        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator->errors());
        }

        try {
            $operationSchedule = OperationSchedule::create([
                'code' => $request->input('code'),
                'description' => $request->input('description'),
                'start_date' => $request->input('start_date'),
                'end_date' => $request->input('end_date'),
                'enabled' => $request->input('enabled')
            ]);

            flash()->addSuccess(__('backend.generic.store.ok'));

            logDebug('created operation schedule' . $operationSchedule->code);
            logDebug('finish');

            return redirect()->route('cbs.operation-schedules.index', ['type' => $type, 'cbId' => $cbId]);
        } catch (QueryException|Exception|\Throwable $e) {
            logError($e->getMessage() .' at line '. $e->getLine());
        }
        flash()->addError(__('backend.generic.store.error'));
        return redirect()->back()->withInput();
    }


    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Empatia\Cbs\OperationSchedule  $operationSchedules
     * @return \Illuminate\Http\Response
     */
    public function show($type, $cbId, $code)
    {
        $cb = Cb::findOrfail($cbId);

        OperationSchedule::linkModel($cb);

        $model = OperationSchedule::findOrFail($code);

        $this->authorize('view', $model);

        if ($type != 'all') {
            $this->cbType = HCb::validateType($type);
        }

        $view = "backend.empatia.cbs.$type.operation-schedules.operation-schedule";

        if (!view()->exists($view)) {
            $view = "backend.empatia.cbs.default.operation-schedules.operation-schedule";
        }

        return view($view,[
            'type' => $type,
            'cbId' => $cbId,
            'code' => $code,
            'model' => $model,
            'action' => HForm::$SHOW,
            'title' => __($this->prefix.'show.title'),
        ]);
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Empatia\Cbs\OperationSchedule  $operationSchedules
     * @return \Illuminate\Http\Response
     */
    public function edit($type, $cbId, $code)
    {
        $cb = Cb::findOrfail($cbId);

        OperationSchedule::linkModel($cb);

        $model = OperationSchedule::findOrFail($code);

        $this->authorize('update', $model);

        if ($type != 'all') {
            $this->cbType = HCb::validateType($type);
        }

        $view = "backend.empatia.cbs.$type.operation-schedules.operation-schedule";

        if (!view()->exists($view)) {
            $view = "backend.empatia.cbs.default.operation-schedules.operation-schedule";
        }

        return view($view,[
            'model' => $model,
            'action' => HForm::$EDIT,
            'title' => __($this->prefix.'edit.title'),
            'type' => $type,
            'cbId' => $cbId,
            'code' => $code
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Empatia\Cbs\OperationSchedule  $operationSchedules
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $type, $cbId, $code)
    {
        $cb = Cb::findOrfail($cbId);

        OperationSchedule::linkModel($cb);

        $model = OperationSchedule::findOrFail($code);

        $this->authorize('update', $model);

        if ($type != 'all') {
            $this->cbType = HCb::validateType($type);
        }

        $validator = Validator::make($request->all(), $this->validateRules, $this->validateMessages);
        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator->errors());
        }

        $model->code = $request->input('code') ?? $model->code;
        $model->description = $request->input('description') ?? $model->description;
        $model->start_date = $request->input('start_date') ?? $model->start_date;
        $model->end_date = $request->input('end_date') ?? $model->end_date;
        $model->enabled = $request->input('enabled') ?? $model->enabled;
        try{
            if($model->save()) {
                flash()->addSuccess(__('backend.generic.update.ok'));
                logDebug('updated operation schedule ' . $model->code);
                logDebug('finish');
                return redirect()->route('cbs.operation-schedules.show', ['cbId' => $cbId, 'code' => $code, 'type' => $type]);
            }
        } catch (QueryException|Exception|\Throwable $e) {
            logError('error on update of operation schedule ' . $code);
        }
        flash()->addError(__('backend.generic.update.error'));
        return redirect()->back()->withInput();
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Empatia\Cbs\OperationSchedule  $operationSchedules
     * @return \Illuminate\Http\Response
     */
    public function delete($type, $cbId, $code)
    {
        $cb = Cb::findOrfail($cbId);

        OperationSchedule::linkModel($cb);

        $model = OperationSchedule::findOrFail($code);

        $this->authorize('delete', $model);

        if ($type != 'all') {
            $this->cbType = HCb::validateType($type);
        }

        try {
            if ($model->delete()) {
                flash()->addSuccess(__('backend.generic.destroy.ok'));
                return response()->json(['success' => 'success'], 200);
            }
        } catch (QueryException|Exception|\Throwable $e) {
            logError($e->getMessage() . ' at line ' . $e->getLine());
        }
        flash()->addError(__('backend.generic.destroy.error'));
        return redirect()->back();
    }

    /**
     * Restore the specified resource from storage.
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function restore(string $type, int $cbId, string  $code)
    {
        $cb = Cb::findOrfail($cbId);

        OperationSchedule::linkModel($cb);

        $model = OperationSchedule::findOrFail($code);

        $this->authorize('restore', $model);

        if ($type != 'all') {
            $this->cbType = HCb::validateType($type);
        }

        try {

            if ($model->restore()) {
                flash()->addSuccess(__('backend.generic.restore.ok'));
                return response()->json(['success' => 'success'], 200);
            }
        } catch (QueryException|Exception|\Throwable $e) {
            logError('Restore: ' . json_encode($e->getMessage()) . ' at line ' . $e->getLine());
        }
        flash()->addError(__('backend.generic.restore.error'));
        return redirect()->back()->withInput();
    }

}
