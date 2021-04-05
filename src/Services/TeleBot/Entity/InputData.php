<?php


namespace App\Services\TeleBot\Entity;


class InputData
{
    private $data;

    public function __construct($data = null) {
        if (!$data) {
            $data = file_get_contents('php://input');
        }

        if (!$data) {
            throw new \InvalidArgumentException('Empty data in body!');
        }

        $this->data =  json_decode($data, true);
    }

    public function getChatId() {
        return $this->data['message']['chat']['id'] ?? null;
    }

    public function getUserId() {
        return $this->data['message']['from']['id'] ?? null;
    }

    public function getMessageId() {
        return $this->data['message']['message_id'] ?? null;
    }

    public function getText() {
        return $this->data['message']['text'] ?? null;
    }

    public function getCommand() {
        if (!$this->isBotCommand()) {
            return null;
        }

        return explode(' ', $this->data['message']['text'])[0];
    }

    public function textLike($like) {
        return $this->getText() && strpos($this->getText(), $like) !== false;
    }

    public function isBotCommand() : bool {
        return $this->data['message']['entities'][0]['type'] === 'bot_command';
    }

    public function __toString() : string {
        return json_encode($this->data);
    }
}