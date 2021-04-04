<?php


namespace App\Services\TeleBot\Entity;


class InputData
{
    private $data;

    public function __construct(array $data = []) {
        if (empty($data) && !$data = json_decode(file_get_contents('php://input'), true)) {
            throw new \InvalidArgumentException('Empty data in body!');
        }

        $this->data =  $data;
    }

    public function getChatId() {
        return $this->data['message']['chat']['id'] ?? null;
    }

    public function getMessageId() {
        return $this->data['message']['message_id'] ?? null;
    }

    public function getText() {
        return $this->data['message']['text'] ?? null;
    }

    public function isBotCommand() : bool {
        return $this->data['message']['entities'][0]['type'] === 'bot_command';
    }

    public function __toString() : string {
        return json_encode($this->data);
    }
}