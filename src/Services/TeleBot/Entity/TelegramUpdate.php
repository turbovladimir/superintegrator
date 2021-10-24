<?php

namespace App\Services\TeleBot\Entity;

use App\Services\TeleBot\Exception\UpdateParseError;

class TelegramUpdate
{
    /**
     * @var array
     */
    private $data;

    public function __construct(string $rawData)
    {
        $this->setData($rawData);
    }

    public function toArray() : array
    {
        return $this->data;
    }

    public function updateId() : int
    {
        return $this->getPropertyByPath('update_id');
    }

    public function getCommand() : string
    {
        $command = $this->getPropertyByPath('message.command', false);

        if ($command) {
            return ltrim($command, '/');
        }

        return '';
    }

    public function fromId() : int
    {
        return $this->getPropertyByPath('message.from.id');
    }

    public function chatId() : int
    {
        return $this->getPropertyByPath('message.chat.id');
    }

    public function messageId() : int
    {
        return $this->getPropertyByPath('message.message_id');
    }

    public function text() : string
    {
        return $this->getPropertyByPath('message.text', false) ?? '';
    }

    private function getPropertyByPath(string $path, bool $strict = true)
    {
        if (empty($pathData = explode('.', $path))) {
            throw new UpdateParseError('The path must contains comma delimiter!');
        }

        $data = $this->data;

        foreach ($pathData as $key) {
            if (empty($data[$key])) {
                if($strict) {
                    throw new UpdateParseError(
                        sprintf('Trying to fetch value by path `%s` data: `%s`',
                            $path, json_encode($this->data, JSON_PRETTY_PRINT))
                    );
                } else {
                    return  null;
                }
            }

            $data = $data[$key];
        }

        return $data;
    }

    private function setData(string $data)
    {
        if (empty($data) || !($data = json_decode($data, true))) {
            throw new UpdateParseError(
                sprintf('Invalid or empty data: `%s`', json_encode($this->data, JSON_PRETTY_PRINT))
            );
        }

        $this->data = $data;
        $this->messageId();
        $this->updateId();
    }
}