<?php

namespace App\Listeners\Empatia\Frontend;


use App\Events\Empatia\Frontend\UserCreated;
use App\Http\Controllers\Backend\Notifications\NotificationsController;
use App\Models\Backend\Notifications\Template;
use App\Models\User;

class UserEventSubscriber
{


    /**
     * Register the listeners for the subscriber.
     *
     * @param  \Illuminate\Events\Dispatcher  $events
     * @return array
     */

    /**
     * Handle new User.
     */
    public function handleUserCreated($event){
        $user = $event->user;

        $tagsAccountCreated = ['name' => $user->name];
        $templateAccountCreated = Template::whereCode('account_created')->whereChannel('email')->first();
        NotificationsController::createNotification($templateAccountCreated, $tagsAccountCreated, $user->email, $user->id, null, null);

        $usersWithRole = User::getByRole("laravel-manager"); //get users with role
        foreach ($usersWithRole as $userRole){
            $fullName = $userRole->getfullName();
            $tagsAccountCreatedManager = ['manager-name'=> $fullName,'name' => $user->name];
            $templateAccountCreatedManager= Template::whereCode('account_created_manager')->whereChannel('email')->first();
            NotificationsController::createNotification($templateAccountCreatedManager, $tagsAccountCreatedManager, getField($userRole,"email"), getField($userRole,"id"), null, null);
        }
    }

    public static function subscribe($events)
    {
        return [
            UserCreated::class => 'handleUserCreated'
        ];
    }
}
