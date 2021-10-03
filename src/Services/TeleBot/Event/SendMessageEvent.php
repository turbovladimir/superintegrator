<?php

namespace App\Services\TeleBot\Event;

use App\Entity\Conversation;

class SendMessageEvent extends SendDataTelegramEvent
{
    private $text;

    public function __construct(Conversation $conversation, string $text)
    {
        $this->text = $text;
        parent::__construct($conversation);
    }

    public function getData(): array
    {
        return parent::getData() + [
            'text' => $this->text
        ];
    }

    public function getTelegramMethod(): string
    {
        return 'sendMessage';
    }
}