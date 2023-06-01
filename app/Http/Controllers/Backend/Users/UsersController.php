<?php

namespace App\Http\Controllers\Backend\Users;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Livewire\Component;
use Livewire\WithPagination;
use App\Helpers\HKeycloak;
use Arr;

class UsersController extends Component
{
    // Controller prefix
    private $prefix = "backend.users.";

    protected $listeners = ['toggleSort'];

    private $sortDesc = true;
    public $search;

    public $origUsers;
    public $users;

    function __construct($id = null)
    {
        parent::__construct($id);
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        return view($this->prefix.'index', [
            'title' => __($this->prefix.'title.index'),
        ]);
    }

    public function mount() {
        $this->origUsers = HKeycloak::getUsers();
    }

    public function render() {
        $this->users = $this->origUsers;

        usort($this->users, function($a, $b) {
            if($this->sortDesc)
                return strcasecmp($a["firstName"], $b["firstName"]);
            else
                return strcasecmp($b["firstName"], $a["firstName"]);
        });

        if(!empty($this->search)) {
            $this->users = array_filter($this->users, function($k) {
                if(array_key_exists('firstName', $k) && stripos($k["firstName"], $this->search) !== false) return true;
                if(array_key_exists('lastName', $k) && stripos($k["lastName"], $this->search) !== false) return true;
                if(array_key_exists('email', $k) && stripos($k["email"], $this->search) !== false) return true;
                return false;
            });
        }

        return view('livewire.backend.users.list');
    }

    public function toggleSort() {
        $this->sortDesc = ! $this->sortDesc;
    }
}
