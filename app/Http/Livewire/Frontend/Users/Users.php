<?php

namespace App\Http\Livewire\Frontend\Users;

use App\Helpers\HBackend;
use App\Helpers\HFrontend;
use App\Helpers\HKeycloak;
use App\Http\Livewire\Users\Exception;
use App\Http\Requests\UpdateUserDetailsLivewire;
use App\Http\Requests\UpdateUserGenericDataLivewire;
use App\Http\Requests\UpdateUserPasswordLivewire;
use App\Models\Empatia\Cbs\Topic;
use App\Models\User;
use Arr;
use Session;
use Cache;
use Carbon\Carbon;
use Doctrine\DBAL\Query\QueryException;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Livewire\Component;


class Users extends Component
{
    private $prefix = 'frontend.users.profile.';
    public $projectPath;

    public $user;
    public $userName;
    public $userParameters;
    public $userProposals;
    public $confirmEmail;
    public $viewMode = 'show';
    public $activeTab;
    public $tabs;


    protected function rules()
    {
        if ($this->activeTab == 'generic') return (new UpdateUserGenericDataLivewire)->rules();
        if ($this->activeTab == 'details') return (new UpdateUserDetailsLivewire)->rules();
        if ($this->activeTab == 'password') return (new UpdateUserPasswordLivewire)->rules();

    }

    public function mount()
    {
        $this->tabs = (array)HFrontend::getTabsToShow();

        if (empty($this->activeTab)) {
            $this->activeTab = getField($this->tabs, 'generic.code');
        }
        if (getField($this->tabs, $this->activeTab)) {

            if ($this->activeTab == getField($this->tabs, 'details.code')) {
                $this->userParameters = HFrontend::getConfigurationByCode('user_parameters');
            }

            if ($this->activeTab == getField($this->tabs, 'proposals.code')) {
                $this->userProposals = HFrontend::getUserProposals();
            }
        }
        $this->userName = getField($this->user, 'name');
    }

    public function render()
    {
        return view("frontend.$this->projectPath.livewire.users.profile");
    }

    /**
     * Update user generic data
     */
    public
    function updateUserGenericData()
    {
        try {
            $validatedData = $this->validate();
            $user = User::whereId(auth()->user()->id)->first();
            $data['firstName'] = getField($validatedData, 'user.firstName');
            $data['lastName'] = getField($validatedData, 'user.lastName');
            $name = getField($validatedData, 'user.firstName') . ' ' . getField($validatedData, 'user.lastName');
            if ($user->updateKeycloakData($data) && $user->updateUserName($name)) {
                self::changeViewMode('show');
                $this->userName = $name;
            }
        } catch (QueryException|Exception|\Throwable $e) {
            logError($e->getMessage() . ' at line: ' . $e->getLine());
            throw $e;
        }
    }

    /**
     * Update user details data
     */
    public
    function updateUserDetails()
    {
        try {
            $validatedData = $this->validate();
            $user = User::whereId(auth()->user()->id)->first();
            $params['parameters'] = [];
            foreach (getField($validatedData, 'user.parameters') ?? [] as $key => $value) {
                $params['parameters'][$key] = $value;
            }
            if ($user->updateParameters($params)) {
                self::changeViewMode('show');
            }
        } catch (QueryException|Exception|\Throwable $e) {
            logError($e->getMessage() . ' at line: ' . $e->getLine());
            throw $e;
        }

    }

    /**
     * Update user password by sending an email
     */
    public
    function updateUserPassword()
    {
        try {
            $validatedData = $this->validate();
            $user = User::whereId(auth()->user()->id)->first();
            if ($user->sendPasswordChangeRequest(auth()->user()->uuid)) {
                $this->confirmEmail = null;
                self::changeViewMode('show');

                //TODO: change to notifications
                session()->flash('success', __($this->prefix . 'email-sent.label'));
            }
        } catch (QueryException|Exception|\Throwable $e) {
            logError($e->getMessage() . ' at line: ' . $e->getLine());
            throw $e;
        }
    }

    /**
     * Change profile tab to show
     */
    public
    function changeTabToShow($newTab)
    {
        $this->activeTab = $newTab;
        $this->viewMode = 'show';

        if (getField($this->tabs, $this->activeTab)) {

            if ($this->activeTab == getField($this->tabs, 'details.code')) {
                $this->userParameters = HFrontend::getConfigurationByCode('user_parameters');
            }

            if ($this->activeTab == getField($this->tabs, 'proposals.code')) {
                $this->userProposals = HFrontend::getUserProposals();
            }
        }
    }

    /**
     * Change profile view mode
     */
    public
    function changeViewMode($action)
    {
        $this->viewMode = $action;
    }
}



