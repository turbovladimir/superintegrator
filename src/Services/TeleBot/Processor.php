<?php


namespace App\Services\TeleBot;


use Longman\TelegramBot\Entities\Update;
use Longman\TelegramBot\Telegram;

class Processor extends Telegram
{

    public function __construct(string $allowUsers, $api_key, $bot_username = '') {
        parent::__construct($api_key, $bot_username);
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
    }
}