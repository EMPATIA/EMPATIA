<?php

namespace App\Http\Controllers\Backend\Notifications;

use Doctrine\DBAL\Query\QueryException;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\Backend\Notifications\Email;
use App\Helpers\HBackend;
use App\Helpers\HForm;
use Arr;
use App\Http\Controllers\Backend\Controller;

class EmailsController extends Controller
{
    // Controller prefix
    private $prefix = "backend.notifications.emails.";

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
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        $email = Email::whereId($id)->firstOrFail();

        return view($this->prefix.'email',[
            'email' => $email,
            'action' => HForm::$SHOW,
            'title' => __($this->prefix.'show.title'),
        ]);
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
            if(Email::findOrFail($id)->delete()) {
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
        try {
            DB::beginTransaction();
            if (Email::withTrashed()->findOrFail($id)->restore()) {
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
