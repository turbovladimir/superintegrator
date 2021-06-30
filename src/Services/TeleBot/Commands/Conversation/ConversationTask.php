<?php


namespace App\Services\TeleBot\Commands\Conversation;


use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Conversation;
use Longman\TelegramBot\Entities\Message;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Entities\Update;
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
        $conversation = new Conversation(
            $message->getFrom()->getId(), $message->getChat()->getId(), $this->getName());

        return $this->executeCommand($message, $conversation);
    }

    abstract protected function executeCommand(Message $message, Conversation $conversation) : ServerResponse;
}