<?php


namespace App\Services\TeleBot\Entity;


class InputData
{
    private $data;
    private $type;

    public function __construct($data = null) {
        if (!$data) {
            $data = file_get_contents('php://input');
        }

        if (!$data) {
            throw new \InvalidArgumentException('Empty data in body!');
        }

        $data =  json_decode($data, true);
        $this->type = 'message';

        if (!empty($data['callback_query'])) {
            $this->type = 'callback_query';
            $this->data = $data['callback_query']['message'];
        } else {
            $this->type = 'message';
            $this->data = $data['message'];
        }
    }

    /**
     * @return int|string|null
     */
    public function getType() {
        return $this->type;
    }

    public function getChatId() {
        return $this->data['chat']['id'] ?? null;
    }

    public function getUserId() {
        return $this->data['from']['id'] ?? null;
    }

    public function getMessageId() {
        return $this->data['message_id'] ?? null;
    }

    public function getText() {
        return $this->data['text'] ?? null;
    }

    public function getCommand() {
        if (!$this->isBotCommand()) {
            return null;
        }

        return explode(' ', $this->data['text'])[0];
    }

    public function textLike($like) {
        return $this->getText() && strpos($this->getText(), $like) !== false;
    }

    public function isBotCommand() : bool {
        return $this->data['entities'][0]['type'] === 'bot_command';
    }

    public function __toString() : string {
        return json_encode($this->data);
    }
}