<?php

namespace App\Commands\TeleBot\Conversation;

use Longman\TelegramBot\Conversation;
use Longman\TelegramBot\DB;
use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Entities\Message;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\TelegramLog;
use PDO;
use PDOException;

class ClearChatCommand extends ConversationTask
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


    protected function executeCommand(Message $message, Conversation $conversation): ServerResponse {
        $chatId = $message->getChat()->getId();
        $userId = $message->getFrom()->getId();
        $text = trim($message->getText(true));
        // Load any existing notes from this conversation
        $notes = &$conversation->notes;
        !is_array($notes) && $notes = [];

        // Load the current state of the conversation
        $state = $notes['state'] ?? 0;
        // Preparing response
        $data = [
            'chat_id'      => $chatId,
            'reply_markup' => Keyboard::remove(['selective' => true]),
        ];


        switch ($state) {
            case 0:
                $keys = ['Clear messages', 'Truncate tables', 'Clear and truncate', 'NO'];

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
                } elseif($notes['answer'] === 'Clear messages') {
                    $messageCount = $this->clearMessages(DB::getPdo(), $userId, $chatId);
                    $data['text'] = "Clear messages: {$messageCount}" . PHP_EOL;
                } elseif($notes['answer'] === 'Truncate tables') {
                    $data['text'] = $this->truncateTables(DB::getPdo());
                } elseif($notes['answer'] === 'Clear and truncate') {
                    $messageCount = $this->clearMessages(DB::getPdo(), $userId, $chatId);
                    $data['text'] = "Clear messages: {$messageCount}" . PHP_EOL;
                    $data['text'] .= $this->truncateTables(DB::getPdo());
                }

                TelegramLog::debug("Conversation stopped?: {$conversation->stop()}");
                $result = Request::sendMessage($data);

                break;
        }

        return $result;
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
