<?php
namespace App\Commands\TeleBot\Conversation;

use App\Services\TeleBot\TelegramWebDriver;
use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Entities\Message;
use Longman\TelegramBot\Entities\ServerResponse;

class CancelCommand extends ConversationCommand
{
    public function executeCommand(Message $message): ServerResponse
    {
        $data = $this->createResponseData();
        $data['reply_markup'] = Keyboard::remove(['selective' => true]);
        $this->closeConversation();

        $response = TelegramWebDriver::sendMessage($data);

        return $response;
    }
}
