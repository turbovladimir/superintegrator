<?php


namespace App\Services\TeleBot;


use Longman\TelegramBot\Entities\Update;
use Longman\TelegramBot\Telegram;
use Longman\TelegramBot\TelegramLog;
use Psr\Log\LoggerInterface;
use Traversable;

class Processor extends Telegram
{

    public function __construct(
        Traversable $commands,
        string $allowUsers,
        string $api_key,
        string $bot_username,
        LoggerInterface $telebotDebugLogger,
        LoggerInterface $telebotUpdatesLogger
    ) {
        parent::__construct($api_key, $bot_username);
        TelegramLog::initialize($telebotDebugLogger, $telebotUpdatesLogger);
        TelegramLog::$always_log_request_and_response = true;
        $allowUsers = explode(',', $allowUsers);

        if (!empty($allowUsers)) {
            $this->setUpdateFilter(
                function (
                    Update $update,
                    Telegram $telegram,
                    &$reason = 'Update denied by update_filter'
                ) use ($allowUsers){
                    $user_id = $update->getMessage()->getFrom()->getId();
                    if (in_array($user_id, $allowUsers)) {
                        return true;
                    }

                    $reason = "Invalid user with ID {$user_id}";
                    return false;
                });
        }

         $this->commands_objects = iterator_to_array($commands);
    }

    public function getCommandsList() {
        return $this->commands_objects;
    }

    public function executeCommand($command) {
        if (empty($this->commands_objects[$command])) {
            throw new \InvalidArgumentException("The command {$command} not configured!");
        }

        return $this->last_command_response = $this->commands_objects[$command]->execute($this->update);
    }
}