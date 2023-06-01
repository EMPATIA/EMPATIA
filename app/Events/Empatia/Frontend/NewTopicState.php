<?php

namespace App\Events\Empatia\Frontend;

use App\Models\Empatia\Cbs\Topic;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewTopicState
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The topic instance.
     *
     * @var App\Models\Empatia\Cbs\Topic
     */
    public Topic $topic;
    public $state;


    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Topic $topic, $state)
    {
        $this->topic = $topic;
        $this->state = $state;
    }
}
