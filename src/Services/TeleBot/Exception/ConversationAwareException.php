<?php

namespace App\Services\TeleBot\Exception;

use App\Entity\Conversation;

abstract class ConversationAwareException extends \Exception
{
    /**
     * @var Conversation
     */
    private $conversation;

    public function __construct($message, Conversation $conversation)
    {
        parent::__construct($message);
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