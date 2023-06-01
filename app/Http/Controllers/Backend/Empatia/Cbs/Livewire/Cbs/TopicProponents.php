<?php

namespace App\Http\Controllers\Backend\Empatia\Cbs\Livewire\Cbs;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Modules\Cbs\Http\Livewire\Exception;
use Modules\Cbs\Http\Livewire\QueryException;
use App\Models\Empatia\Cbs\Topic;
use Modules\Notifications\Http\Controllers\NotificationsController;

class TopicProponents extends Component
{
//    // Validation rules and messages
//    private $validateRules = [];
//    private $validateMessages = [];
//
//    private $validateRulesEdit = [];
//    private $validateMessagesEdit = [];

    private array $guarded = [];

    //<editor-fold desc="Properties">
    public $topicId;
    public $topic;

    public $userId = null;
    public $roles;
    public $userIdDelete;
    public $newUser = true;
    public $primaryDeleted = null;
    public $usersList;
    public $proponentsList = [];
    public $proponentsDetails = [];

    public $name;
    public $email;
    public $role;
    public $phone;
    public $primary;
    //</editor-fold>

    protected $listeners = [
        'deleteProponent' => 'delete',
        'changePrimaryUser',
        'chooseUser'
    ];

    public function chooseUser($id) {
        if($this->userId != $id){
            $this->userId = $id;
        }else{
            $this->userId = null;
        }
    }

    public function mount() {
        $this->topic = Topic::findOrFail($this->topicId);
        $this->proponentsList = $this->topic->proponents;
    }

    public function render()
    {
        return view('empatia::cbs.livewire.topic.topic-proponents');
    }

    public function newProponent($userExists) {
        try {
            $this->newUser = !$userExists;
            if ($userExists){
                $this->usersList = [];
                foreach (User::select('id', 'name')->get() as $user){
                    foreach ($this->proponentsList as $proponent)
                        if($user->id == $proponent['user_id'])
                            continue;
                    $this->usersList[$user->id] = $user->name;
                }
            }
            $this->primaryDeleted = null;
            $this->dispatchBrowserEvent('show-modal-user');
        } catch (QueryException|Exception|\Throwable $e) {
            logError( json_encode($e->getMessage()));
        }
    }

    public function store(){
        try {
            //create user
            if($this->newUser){
                $newUserPassword = substr(md5(time()), 0, 16);
                $user = User::create([
                    'name' => $this->name,
                    'email' => strtolower($this->email),
                    'password' => bcrypt($newUserPassword),
                ]);

//                $tags = [
//                    'user-name' => $user->name,
//                    'password' => $newUserPassword
//                ];
                //TODO: NOTIFICATION TO NEW PROPONENT CREATION
//                NotificationsController::createNotification('user_creation', $tags, 'email', $user->email, $user->id);
                //TODO: NOTIFICATION TO RESET PASSWORD

            }else{
                $user = User::findOrFail($this->userId);
            }

            $topic = Topic::findOrFail($this->topicId);
            $proponents = getField($topic, 'proponents');

            //check if the user already exists
            foreach ((array)$proponents as $proponent) {
                if ($proponent->user_id == $user->id) {
                    $this->dispatchBrowserEvent('proponentsUserAlreadyExists'); //Show warning with bootbox
                    return;
                }
            }
            //check if is primary (to ensure that only was one removes the others)
            if($this->primary) foreach ((array)$proponents as $proponent) $proponent->primary = null;

            //check if is the first one to make primary by default
            if(empty((array)$proponents)) $this->primary = "1";
            $data = [];
            $proponents[] = (object)['user_id' => $user->id, 'primary' => (int)$this->primary, 'created_by' => \Auth::id(), 'data' => json_encode($data)];

            DB::beginTransaction();
            $topic->update(['proponents' => (array)$proponents]);
            DB::commit();

            $this->proponentsList = $topic->proponents;
            $this->dispatchBrowserEvent('close-modals');

        } catch (QueryException | Exception  | \Throwable $e) {
            DB::rollback();
            logError('store: ' . json_encode($e->getMessage()));
        }
    }

    public function delete($index)
    {
        try {
            if (count($this->proponentsList) <= $index) return;         // If it's a wrong index, leaves
            if (count($this->proponentsList) <= 1) return;              // If there is just one proponent, leaves
            if ($this->proponentsList[$index]['primary']) {             // If the proponent to delete is the primary
                $this->primaryDeleted = $index;

                $tempProponentsList = $this->proponentsList;            // Creates proponents temporary list
                unset($tempProponentsList[$this->primaryDeleted]);      // Removes primary proponent from the proponents temporary list
                $tempProponentsList = array_values($tempProponentsList);// Resets the proponents temporary list
                $this->usersList = [];
                foreach (User::select('id', 'name')->get() as $user) {
                    foreach ($tempProponentsList as $proponent) {
                        if ($user->id == $proponent['user_id']) {         // Generates a new proponents list, to show in the select option of the next primary user
                            $this->usersList[$user->id] = $user->name;
                        }
                    }
                }
                $this->dispatchBrowserEvent('show-modal-user');
            }else{                                                      // If the proponent to delete isn't the primary or if changePrimaryUserOnDelete function was called
                unset($this->proponentsList[$index]);                   // Removes the previous primary proponent from the original proponents list
                $this->proponentsList = array_values($this->proponentsList);
                DB::beginTransaction();
                $this->topic = Topic::findOrFail($this->topicId)->update(['proponents' => $this->proponentsList]);
                DB::commit();
            }
        } catch (QueryException|Exception|\Throwable $e) {
            DB::rollback();
            logError('store: ' . json_encode($e->getMessage()));
        }
    }

    public function changePrimaryUserOnDelete()
    {
        try {
            foreach ($this->proponentsList as $key => $proponent) {
                if ($proponent['user_id'] == $this->userIdDelete)
                    $this->proponentsList[$key]['primary'] = 1;
            }
            $this->proponentsList[$this->primaryDeleted]['primary'] = null;
            $this->delete($this->primaryDeleted);
        } catch (QueryException|Exception|\Throwable $e) {
            logError(json_encode($e->getMessage()));
        }
    }

    public function changePrimaryUser($index){
        try {
            foreach ($this->proponentsList as $key => $proponent) {
                if ($proponent['primary'])
                    $this->proponentsList[$key]['primary'] = null;
            }
            $this->proponentsList[$index]['primary'] = 1;
            Topic::findOrFail($this->topicId)->update(['proponents' =>  array_values($this->proponentsList)]);
        } catch (QueryException | Exception  | \Throwable $e) {
            logError(json_encode($e->getMessage()));
        }
    }
}
