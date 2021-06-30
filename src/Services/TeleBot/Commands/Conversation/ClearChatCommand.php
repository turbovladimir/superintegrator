<?php

namespace App\Services\TeleBot\Commands\Conversation;

use Longman\TelegramBot\Conversation;
use Longman\TelegramBot\DB;
use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Entities\Message;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\TelegramLog;
use PDOException;

class ClearChatCommand extends Conversation
{
    const TABLES_FOR_TRUNCATE = [
        'callback_query',
        'conversation',
        'edited_message',
        'message',
        'request_limiter',
        'telegram_update'
    ];

    /**
     * @var string
     */
    protected $description = 'Clear all chat data';


    protected function executeCommand(Message $message, Conversation $conversation): ServerResponse {
        $chatId = $message->getChat()->getId();
        $userId = $message->getFrom()->getId();
        $text = trim($message->getText(true));
        // Load any existing notes from this conversation
        $notes = &$conversation->notes;
        !is_array($notes) && $notes = [];

        // Load the current state of the conversation
        $state = $notes['state'] ?? 0;

        switch ($state) {
            case 0:
                $keys = ['Yes, only clear messages from chat', 'Yes, clear messages and truncate tables', 'NO'];

                if (empty($text) || !in_array($text, $keys , true)) {
                    $data['text'] = 'Are you sure?';
                    $data['reply_markup'] = (new Keyboard($keys))
                        ->setResizeKeyboard(true)
                        ->setOneTimeKeyboard(true)
                        ->setSelective(true);
                    $result = Request::sendMessage($data);

                    break;
                }

                $notes['answer'] = $text;

                $notes['state'] = 1;
                $conversation->update();

            case 1:
                if ($notes['answer'] === 'NO') {
                    $conversation->stop();
                    $data['text'] = 'Okay, miss click happens';
                    $result = Request::sendMessage($data);

                    break;
                } else {
                    $pdo = DB::getPdo();
                    $messageIds = [];

                    foreach ([
                        'select message_id from edited_message where user_id = %d and chat_id = %d',
                        'select id from message where user_id = %d and chat_id = %d',
                             ] as $query) {
                        $messageIds = array_merge($messageIds,
                            $pdo->query(sprintf($query, $userId, $chatId))->fetchAll());
                    }

                    foreach ($messageIds as $id) {
                        Request::deleteMessage([
                            'chat_id' => $chatId,
                            'message_id' => $id,
                        ]);
                    }

                    $data['text'] = "Clear messages: " . count($messageIds) . PHP_EOL;

                    if($notes['answer'] === 'Yes, clear messages and truncate tables') {
                        $pdo->beginTransaction();

                        try {
                            foreach (self::TABLES_FOR_TRUNCATE as $table) {
                                $pdo->query("Truncate table {$table}");
                            }

                            $pdo->commit();
                            $data['text'] .= "Database successfully cleared";
                        } catch (PDOException $e) {
                            $data['text'] .= "*Database cleanup failed!* {$e->getMessage()}";
                            $pdo->rollBack();
                            TelegramLog::error($e->getMessage());
                        }
                    }
                    }

                $result = Request::sendMessage($data);
        }

        return $result;
    }
}
