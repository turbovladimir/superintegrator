<?php

namespace App\Services\TeleBot\Event;

use App\Entity\Conversation;

class DeleteMessageEvent extends SendDataTelegramEvent
{
    private $messageId;

    public function __construct(Conversation $conversation, int $messageId)
    {
        $this->messageId = $messageId;
        parent::__construct($conversation);
    }

    public function getData(): array
    {
        return parent::getData() + [
            'message_id' => $this->messageId
        ];
    }

    public function getTelegramMethod(): string
    {
        return 'deleteMessage';
    }
}