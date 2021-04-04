<?php


namespace App\Services\TeleBot\Entity;


class InputData
{
    private $data;

    public function __construct() {
        if (!$data = json_decode(file_get_contents('php://input'), true)) {
            throw new \InvalidArgumentException('Empty data in body!');
        }

        $this->data =  $data;
    }

    public function getChatId() {
        return $this->data['message']['chat']['id'] ?? null;
    }

    public function getMessage() {
        return $this->data['message']['text'] ?? null;
    }

    public function __toString() : string {
        return json_encode($this->data);
    }
}