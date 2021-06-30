<?php


namespace App\Services\TeleBot;


use Longman\TelegramBot\Entities\Update;
use Longman\TelegramBot\Telegram;
use Longman\TelegramBot\TelegramLog;
use Psr\Log\LoggerInterface;

class Processor extends Telegram
{

    public function __construct(
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

//        if (!empty($allowUsers)) {
//            $this->setUpdateFilter(
//                function (
//                    Update $update,
//                    Telegram $telegram,
//                    &$reason = 'Update denied by update_filter'
//                ) use ($allowUsers){
//                    $user_id = $update->getMessage()->getFrom()->getId();
//                    if (in_array($user_id, $allowUsers)) {
//                        return true;
//                    }
//
//                    $reason = "Invalid user with ID {$user_id}";
//                    return false;
//                });
//        }

         $this->setCommandConfig('cleanup',
             [
                 // Define which tables should be cleaned.
                  'tables_to_clean' => [
                      'message',
                      'edited_message',
                  ],
                 // Define how old cleaned entries should be.
                 'clean_older_than' => [
                      'message'        => '1 days',
                      'edited_message' => '1 days',
                  ]
             ]
        );
    }
}