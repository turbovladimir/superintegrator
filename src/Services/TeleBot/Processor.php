<?php


namespace App\Services\TeleBot;

use App\Commands\TeleBot\Conversation\ConversationCommand;
Use App\Services\TeleBot\Exception\ChatWarning;
use App\Entity\Conversation;
use App\Repository\ConversationRepository;
use App\Services\TeleBot\Event\ConversationStartEvent;
use App\Services\TeleBot\Event\SendMessageEvent;
use App\Services\TeleBot\Event\SendStickerEvent;
use App\Services\TeleBot\Exception\ChatError;
use App\Services\TeleBot\Exception\ConversationAwareException;
use Longman\TelegramBot\TelegramLog;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Throwable;
use Traversable;

class Processor
{
    private $conversationRepository;
    private $allowUsers;

    /**
     * @var ConversationCommand[]
     */
    private $commands;
    private $dispatcher;
    private $telebotDebugLogger;

    public function __construct(
        EventDispatcherInterface $dispatcher,
        ConversationRepository $conversationRepository,
        Traversable $commands,
        string $allowUsers,
        LoggerInterface $telebotDebugLogger
    ) {
        $this->telebotDebugLogger = $telebotDebugLogger;
        $this->dispatcher = $dispatcher;
        $this->conversationRepository = $conversationRepository;
        TelegramLog::$always_log_request_and_response = true;
        $this->allowUsers = explode(',', $allowUsers);
         $this->commands = iterator_to_array($commands);
    }

    public function handle(array $updateData): void {
        $conversation = null;

        try {
            $this->checkConversationParams($conversation = $this->fetchConversationByUpdate($updateData));
            $this->commands[$conversation->getCommand()]->execute($conversation);
        } catch (Throwable $exception) {
            $this->handleExceptions($exception, $updateData);
        }

        $this->dispatcher->dispatch(new SendStickerEvent($conversation, SendStickerEvent::STICKER_TOM_LAUNGHT));
    }

    public function fetchConversationByUpdate(array $updateData) : Conversation {
        $chatId = $updateData['message']['chat']['id'] ?? null;
        $messageId = $updateData['message']['message_id'] ?? null;
        $updateId = $updateData['update_id'] ?? null;
        $userId = $updateData['message']['from']['id'] ?? null;
        $command = null;

        if(!empty($updateData['message']['command'])) {
            $command = ltrim($updateData['message']['command'], '/');
        }

        if (!$command && !empty($updateData['message']['text']) && strpos($updateData['message']['text'], '/') === 0) {
            $command = ltrim($updateData['message']['text'], '/');
        }

        if (!$chatId || !$updateId || !$userId) {
            throw new \InvalidArgumentException('Incorrect data from telegram!');
        }

        $conversation = $this->conversationRepository->findOneBy(['userId' => $userId, 'chatId' => $chatId, 'status' => Conversation::STATUS_OPENED]);

        if (!$conversation) {
            $isNewConversation = true;
            $conversation = (new Conversation())
                ->setUserId($userId)
                ->setChatId($chatId)
                ->setStatus(Conversation::STATUS_OPENED)
                ->setLastModify(new \DateTime())
                ->setLastUpdateId($updateId);
        } else {
            $isNewConversation = false;
            $conversation
                ->setLastUpdateId($updateId)
                ->setLastModify(new \DateTime());
        }

        $conversation->addMessageInHistory($messageId, $command ?? $updateData['message']['text']);
        $this->conversationRepository->save($conversation);
        $this->dispatcher->dispatch(new ConversationStartEvent($conversation));

        if ($isNewConversation) {
            if (!$command) {
                throw new ChatWarning('Please use command for starting our conversation!', $conversation);
            }

            $conversation->setCommand($command);
            $this->conversationRepository->save($conversation);
        }

        return $conversation;
    }

    private function handleExceptions(Throwable $trowable, array $updateData) {
        $logLvl = LogLevel::CRITICAL;

        if ($trowable instanceof ConversationAwareException) {
            $conversation = $trowable->getConversation();
            $this->conversationRepository->cancelConversation($conversation);

            if ($trowable instanceof ChatWarning) {
                $logLvl = LogLevel::WARNING;
                $this->dispatcher->dispatch(new SendStickerEvent($conversation, SendStickerEvent::STICKER_TOM_ANGRY));
                $this->dispatcher->dispatch(new SendMessageEvent($conversation, $trowable->getMessage()));
            } elseif ($trowable instanceof ChatError) {
                $logLvl = LogLevel::ERROR;
                $this->dispatcher->dispatch(new SendStickerEvent($conversation, SendStickerEvent::STICKER_TOM_ERROR));
                $this->dispatcher->dispatch(new SendMessageEvent($conversation, $trowable->getMessage()));
            }
        }

        $this->telebotDebugLogger->log($logLvl, $trowable->getMessage(), $updateData);

        throw $trowable;
    }

    private function checkConversationParams(Conversation $conversation) {
        if (!in_array($conversation->getUserId(), $this->allowUsers)) {
            throw new ChatError("I'm sorry, who are you?", $conversation);
        }

        $commandName = $conversation->getCommand();

        if (!$commandName || !isset($this->commands[$commandName])) {
            throw new ChatError("Command not found or not initialize!", $conversation);
        }
    }
}