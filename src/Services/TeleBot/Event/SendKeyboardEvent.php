<?php

namespace App\Services\TeleBot\Event;

use App\Entity\Conversation;
use App\Entity\Telebot\Button;

class SendKeyboardEvent extends SendDataTelegramEvent
{
    /**
     * @var Button[]
     */
    private $buttons;

    public function __construct(Conversation $conversation, array $buttons)
    {
        $this->buttons = $buttons;
        parent::__construct($conversation);
    }

    public function getData(): array
    {
        foreach ($this->buttons as $button) {
            $rows[] = [$button->get()];
        }

        return parent::getData() + [
            'reply_markup' => [
                "resize_keyboard" => true,
                "one_time_keyboard" => true,
                'inline_keyboard' => $rows ?? []
            ]
        ];
    }

    public function getTelegramMethod(): string
    {
        return 'sendMessage';
    }
}