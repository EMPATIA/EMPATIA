<?php

namespace App\Events\Empatia\Frontend;

use App\Models\Empatia\Cbs\Topic;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TopicCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The topic instance.
     *
     * @var App\Models\Empatia\Cbs\Topic
     */
    public Topic $topic;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Topic $topic)
    {
        $this->topic = $topic;
    }
}
