<?php

namespace App\Services\TeleBot\Event;

use App\Entity\Conversation;
use Symfony\Contracts\EventDispatcher\Event;

abstract class SendDataTelegramEvent extends Event
{
    protected $conversation;

    abstract public function getTelegramMethod() : string;

    public function __construct(Conversation $conversation)
    {
        $this->conversation = $conversation;
    }

    /**
     * @return array
     */
    public function getData(): array {
        return ['chat_id' => $this->conversation->getChatId()];
    }

    /**
     * @return Conversation
     */
    public function getConversation(): Conversation
    {
        return $this->conversation;
    }
}