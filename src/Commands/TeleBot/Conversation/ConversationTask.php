<?php


namespace App\Commands\TeleBot\Conversation;


use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Conversation;
use Longman\TelegramBot\Entities\Message;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Entities\Update;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\Telegram;
use ReflectionClass;

abstract class ConversationTask extends UserCommand
{
    /**
     * @var bool
     */
    protected $need_mysql = true;

    public function __construct(Telegram $telegram, Update $update = null) {
        $this->name =
            strtolower(str_replace('Command', '', (new ReflectionClass($this))->getShortName()));
        $this->usage = "/{$this->name}";
        parent::__construct($telegram, $update);
    }

    public function execute() {
        $message = $this->getMessage() ?: $this->getEditedMessage();
        $chatId = $message->getChat()->getId();
        $conversation = new Conversation(
            $message->getFrom()->getId(), $chatId, $this->getName());

        try {
            $this->executeCommand($message, $conversation);
        } catch (\Throwable $exception) {
            Request::sendMessage([
                'chat_id' => $chatId,
                'text' => $exception->getMessage()
            ]);
            throw $exception;
        }
    }

    abstract protected function executeCommand(Message $message, Conversation $conversation) : ServerResponse;
}