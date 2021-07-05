<?php

namespace App\Commands\TeleBot\Conversation;

use Longman\TelegramBot\DB;
use Longman\TelegramBot\Entities\Message;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\TelegramLog;
use PDO;

class ClearChatCommand extends ConversationCommand
{
    const TABLES_FOR_TRUNCATE = [
        'telegram_update',
        'callback_query',
        //'conversation',
        'edited_message',
        'message',
        'request_limiter',
    ];

    /**
     * @var string
     */
    protected $description = 'Clear all chat data';


    protected function executeCommand(Message $message): ServerResponse {
        $chatId = $message->getChat()->getId();
        $userId = $message->getFrom()->getId();
        $text = $this->getTextMessage();
        $state = $this->getCurrentState();

        $buttons = ['Clear messages', 'Truncate tables', 'Clear and truncate', 'NO'];

        if ($state === 0) {
            if (empty($text) || !in_array($text, $buttons , true)) {
                return $this->createChooseResponse($buttons, 'Are you sure?');
            } else {
                $this->saveAnswer($text);
                $this->stateUp();
            }
        } elseif ($state === 1) {
            $this->conversationStop();

            switch ($text) {
                case 'NO':
                    return Request::sendMessage($this->createResponseData('Okay, miss click happens'));
                case 'Clear messages':
                    return Request::sendMessage($this->createResponseData(sprintf('Clear messages: %d', $this->clearMessages(DB::getPdo(), $userId, $chatId))));
                case 'Truncate tables':
                    return Request::sendMessage($this->createResponseData($this->truncateTables(DB::getPdo())));
                case 'Clear and truncate':
                    $pdo = DB::getPdo();
                    $responseText = $this->clearMessages($pdo, $userId, $chatId);
                    $responseText .= $this->truncateTables($pdo);

                    return Request::sendMessage($this->createResponseData($responseText));
            }
        }

        return Request::emptyResponse();
    }

    private function clearMessages(PDO $pdo, $userId, $chatId) : int {

        $messageIds = [];

        foreach ([
                     'select message_id from edited_message where user_id = %d and chat_id = %d',
                     'select id from message where user_id = %d and chat_id = %d',
                 ] as $query) {
            $messageIds = array_merge($messageIds,
                $pdo->query(sprintf($query, $userId, $chatId))->fetchAll(PDO::FETCH_COLUMN));
        }

        foreach ($messageIds as $id) {
            Request::deleteMessage([
                'chat_id' => $chatId,
                'message_id' => $id,
            ]);
        }

        return count($messageIds);
    }

    private function truncateTables(PDO $pdo) : string {
        $pdo->beginTransaction();

        try {
            foreach (self::TABLES_FOR_TRUNCATE as $table) {
                $pdo->query("SET FOREIGN_KEY_CHECKS = 0; Truncate {$table};");
            }

            $pdo->commit();
            $result = "Database successfully cleared";
        } catch (\Throwable $e) {
            $result = "*Database cleanup failed!* {$e->getMessage()}";
            $pdo->rollBack();
            TelegramLog::error($e->getMessage());
        }

        return $result;
    }
}
