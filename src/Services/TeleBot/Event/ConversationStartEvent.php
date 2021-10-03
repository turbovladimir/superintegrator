<?php

namespace App\Services\TeleBot\Event;

use App\Entity\Conversation;
use Symfony\Contracts\EventDispatcher\Event;

class ConversationStartEvent extends Event
{
    /**
     * @var Conversation
     */
    private $conversation;

    public function __construct(Conversation $conversation) {
        $this->conversation = $conversation;
    }

    /**
     * @return Conversation
     */
    public function getConversation(): Conversation
    {
        return $this->conversation;
    }
}