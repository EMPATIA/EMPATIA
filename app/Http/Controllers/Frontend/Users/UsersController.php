<?php

namespace App\Http\Controllers\Frontend\Users;

use App\Helpers\Empatia\Cbs\HCb;
use App\Helpers\HBackend;
use App\Helpers\HFrontend;
use App\Helpers\HKeycloak;
use App\Http\Controllers\Backend\CMS\LanguagesController;
use App\Http\Controllers\Frontend\Empatia\Cbs\CbsController;
use App\Http\Requests\UpdateUserDetails;
use App\Http\Requests\UpdateUserGenericData;
use App\Http\Requests\UpdateUserPassword;
use App\Models\Empatia\Cbs\Topic;
use App\Models\User;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Livewire\WithPagination;
use Arr;
use Illuminate\Validation\Rule;
use Route;
use Carbon\Carbon;

class UsersController extends Controller
{
    // Controller prefix
    private $prefix = "frontend.users.profile.";

    // Validation rules and messages
    private $validateRules = [];
    private $validateMessages = [];

    public $tabs;


    /**
     * Displays the chosen profile tab
     * @param string|null $tab
     * @return Renderable
     */
    public function show(string $tab = null)
    {
        try {
            if (auth()->user()->hasAnyRole(['laravel-user', 'laravel-admin'])) {

                $tabs = HFrontend::getTabsToShow();
                $projectPath = HFrontend::getProjectPath(false);

                if (empty($tab)) {
                    $tab = getField($tabs, 'generic.code');
                }
                if (getField($tabs, $tab)) {

                    $localUser = User::whereId(auth()->user()->id)->first();
                    $user = HFrontend::getUserDetails($localUser, $tab);

                    if ($tab == getField($tabs, 'details.code')) {
                        $userParameters = HFrontend::getConfigurationByCode('user_parameters');
                    }

                    if ($tab == getField($tabs, 'proposals.code')) {
                        $userProposals = HFrontend::getUserProposals();
                    }

                    return view("frontend.$projectPath.users.profile", [
                        'content' => null,
                        'projectPath' => $projectPath,
                        'tabs' => $tabs,
                        'activeTab' => $tab,
                        'user' => $user,
                        'userParameters' => $userParameters ?? [],
                        'userProposals' => $userProposals ?? []
                    ]);
                }
                abort(500);
            }
            abort(403);
        } catch (QueryException|Exception|\Throwable $e) {
            logError($e->getMessage() . ' at line: ' . $e->getLine());
            abort(401);
        }

    }


    /**
     * Displays user profile in edit mode
     * @param string|null $tab
     * @return Renderable
     */
    public function edit(string $tab = null)
    {
        try {
            if (auth()->user()->hasAnyRole(['laravel-user', 'laravel-admin'])) {
                $tabs = HFrontend::getTabsToShow();
                $projectPath = HFrontend::getProjectPath(false);

                if (empty($tab)) {
                    $tab = getField($tabs, 'generic.code');
                }
                if (getField($tabs, $tab)) {

                    $localUser = User::whereId(auth()->user()->id)->first();
                    $user = HFrontend::getUserDetails($localUser, $tab);

                    if ($tab == getField($tabs, 'details.code')) {
                        $userParameters = HFrontend::getConfigurationByCode('user_parameters');
                    }

                    if ($tab == getField($tabs, 'proposals.code')) {
                        $userProposals = HFrontend::getUserProposals();
                    }

                    return view("frontend.$projectPath.users.profile", [
                        'content' => null,
                        'projectPath' => $projectPath,
                        'tabs' => $tabs,
                        'activeTab' => $tab,
                        'user' => $user,
                        'userParameters' => $userParameters ?? [],
                        'userProposals' => $userProposals ?? []
                    ]);
                }
                abort(500);
            }
            abort(403);
        } catch (QueryException|Exception|\Throwable $e) {
            logError($e->getMessage() . ' at line: ' . $e->getLine());
            abort(401);
        }

    }

    /**
     * Update user generic data
     *
     * @param \App\Http\Requests\UpdateUserGenericData $request
     * @return Illuminate\Http\Response
     */
    public function updateUserGenericData(UpdateUserGenericData $request)
    {
        try {
            $validated = $request->validated();
            $user = User::whereId(auth()->user()->id)->first();
            $data['firstName'] = getField($validated, 'firstName');
            $data['lastName'] = getField($validated, 'lastName');
            $name = getField($validated, 'user.firstName') . ' ' . getField($validated, 'user.lastName');
            if ($user->updateKeycloakData($data) && $user->updateUserName($name)) {
                return redirect()->action([UsersController::class, 'show'], ['tab' => 'generic']);
            }
            abort(500);
        } catch (QueryException|Exception|\Throwable $e) {
            logError($e->getMessage() . ' at line: ' . $e->getLine());
            abort(401);
        }
    }

    /**
     * Update user details data
     *
     * @param \App\Http\Requests\UpdateUserDetails $request
     * @return Illuminate\Http\Response
     */
    public function updateUserDetails(UpdateUserDetails $request)
    {
        try {
            $validated = $request->validated();
            $params = [];
            $user = User::whereId(auth()->user()?->id ?? 0)->first();
            foreach ($validated ?? [] as $key => $value) {
                $params[$key] = $value;
            }
            if ($user->updateParameters($params)) {
                return redirect()->action([UsersController::class, 'show'], ['tab' => 'details']);
            }
            abort(500);
        } catch (QueryException|Exception|\Throwable $e) {
            logError($e->getMessage() . ' at line: ' . $e->getLine());
            abort(401);
        }

    }

    /**
     * Update user details data
     *
     * @param \App\Http\Requests\UpdateUserPassword $request
     * @return Illuminate\Http\Response
     */
    public function updateUserPassword(UpdateUserPassword $request)
    {
        try {
            $validated = $request->validated();
            $user = User::whereId(auth()->user()->id)->first();
            if ($user->sendPasswordChangeRequest(auth()->user()->uuid)) {
                return redirect()->action([UsersController::class, 'show'], ['tab' => 'password']);
            }
            abort(500);
        } catch (QueryException|Exception|\Throwable $e) {
            logError($e->getMessage() . ' at line: ' . $e->getLine());
            abort(401);
        }
    }

}

