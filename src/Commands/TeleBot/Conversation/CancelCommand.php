<?php
namespace App\Commands\TeleBot\Conversation;

use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Entities\Message;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\TelegramException;

class CancelCommand extends ConversationCommand
{
    public function executeCommand(Message $message): ServerResponse
    {
        return $this->removeKeyboard($this->closeConversation());
    }

    /**
     * Remove the keyboard and output a text.
     *
     * @param string $text
     *
     * @return ServerResponse
     * @throws TelegramException
     */
    private function removeKeyboard($text): ServerResponse
    {
        return $this->replyToChat($text, [
            'reply_markup' => Keyboard::remove(['selective' => true]),
        ]);
    }
}
