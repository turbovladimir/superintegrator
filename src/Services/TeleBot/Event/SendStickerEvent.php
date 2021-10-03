<?php

namespace App\Services\TeleBot\Event;

use App\Entity\Conversation;

class SendStickerEvent extends SendDataTelegramEvent
{
    const STICKER_TOM_ERROR = 'tom_error.webp';
    const STICKER_TOM_ANGRY = 'tom_angry.webp';
    const STICKER_TOM_LAUNGHT = 'tom.webp';

    private $stickerName;

    public function __construct(Conversation $conversation, string $stickerName)
    {
        parent::__construct($conversation);
        $this->stickerName = $stickerName;
    }

    public function getData(): array
    {
        $file = APPLICATION_PATH . "/public/images/telebot/sticker/{$this->stickerName}";

        if (!is_readable($file)) {
            throw new \InvalidArgumentException(sprintf('The file `%s` is not exist', $file));
        }

        return parent::getData() + [
            'sticker' => $file
        ];
    }

    public function getTelegramMethod(): string
    {
        return 'sendSticker';
    }
}