<?php

namespace App\Listeners\Empatia\Frontend;

use App\Events\Empatia\Frontend\NewTopicState;
use App\Events\Empatia\Frontend\TopicCreated;
use App\Http\Controllers\Backend\Notifications\NotificationsController;
use App\Models\Backend\Notifications\Template;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Auth;

class TopicEventSubscriber
{
    /**
     * Handle topic created events.
     */
    public function handleTopicCreated($event) {
        $topic = $event->topic;

        // TODO: check if each of the notifications is enabled; it may make sense to have dynamic notifications

        /*   Notify author   */
        $tagsProposalSubmitted = [
            'name' => Auth::user()->name,
            'topic-title' => data_lang_get($topic, 'title'),
        ];
        $templateProposalSubmitted = Template::whereCode('proposal_submitted')->whereChannel('email')->first();
        NotificationsController::createNotification(
            $templateProposalSubmitted,
            $tagsProposalSubmitted,
            Auth::user()->email,
            Auth::user()->id
        );

        /*   Notify managers   */
        // TODO: not dynamic; different projects may have different manager roles
        $usersWithRole = User::getByRole("laravel-manager"); //get users with role
        foreach ($usersWithRole as $userRole){
            $fullName = $userRole->getfullName();
            $tagsProposalSubmittedManager = [
                'manager-name'          => $fullName,
                'name'                  => Auth::user()->name,
                'topic-title'           => data_lang_get($topic, 'title'),
                'topic-content'         => data_lang_get($topic, 'content'),
                'topic-category'        => implode(", ",$topic->parameterSelectedOptionLabels("category")),
                'topic-estimated-value' => implode(", ",$topic->parameterSelectedOptionLabels("estimated_value"))
            ];
            $templateProposalSubmitted = Template::whereCode('proposal_submitted_manager')->whereChannel('email')->first();
            NotificationsController::createNotification(
                $templateProposalSubmitted,
                $tagsProposalSubmittedManager,
                getField($userRole,"email"),
                getField($userRole,"id")
            );
        }
    }


    /**
     * Handle topic new state.
     */
    public function handleNewTopicState($event){
        $topic = $event->topic;
        $state = $event->state;
        /*   Notify managers   */
        $usersWithRole = User::getByRole("laravel-manager"); //get users with role
        foreach ($usersWithRole as $userRole){
            $fullName = $userRole->getfullName();
            $tagsTopicStatusManager = [
                'name'          => $fullName,
                'status'        => $topic->cb->stateLabel($topic->state, getLang()),
                'topic-title'   => data_lang_get($topic, 'title'),
                ];
            $templateTopicStatusManager = Template::whereCode('proposal_status_changed')->whereChannel('email')->first();
            NotificationsController::createNotification(
                $templateTopicStatusManager,
                $tagsTopicStatusManager,
                getField($userRole,"email"),
                getField($userRole,"id")
            );
        }
        /*   Notify proponents   */
        foreach (getField($topic, 'proponents', []) as $proponent){
            if(isset($proponent->user_id)){
                $user = User::findOrFail($proponent->user_id);
                $tagsTopicStatusProponent = [
                    'name'          => getField($user, 'name', '-'),
                    'status'        => $topic->cb->stateLabel($topic->state, getLang()),
                    'topic-title'   => data_lang_get($topic, 'title'),
                ];
                $templateTopicStatusProponent = Template::whereCode('proposal_status_changed')->whereChannel('email')->first();
                NotificationsController::createNotification(
                    $templateTopicStatusProponent,
                    $tagsTopicStatusProponent,
                    getField($user,"email"),
                    getField($user,"id")
                );
            }
        }
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param  \Illuminate\Events\Dispatcher  $events
     * @return array
     */
    public static function subscribe($events)
    {
        return [
            TopicCreated::class => 'handleTopicCreated',
            NewTopicState::class => 'handleNewTopicState',
        ];
    }
}
