<?php


namespace App\Services\TeleBot;

use App\Commands\TeleBot\Conversation\ConversationCommand;
use App\Entity\Conversation;
use App\Repository\ConversationRepository;
use Doctrine\ORM\EntityManager;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Entities\Update;
use Longman\TelegramBot\TelegramLog;
use Psr\Log\LoggerInterface;
use Traversable;

class Processor
{
    private $botName;
    private $conversationRepository;
    private $allowUsers;
    private $commands;
    private $entityManager;

    public function __construct(
        EntityManager $entityManager,
        ConversationRepository $conversationRepository,
        Traversable $commands,
        string $allowUsers,
        string $apiKey,
        string $botName,
        LoggerInterface $telebotDebugLogger,
        LoggerInterface $telebotUpdatesLogger
    ) {
        $this->entityManager = $entityManager;
        $this->conversationRepository = $conversationRepository;
        $this->botName = $botName;
        TelegramWebDriver::init($botName, $apiKey);
        TelegramLog::initialize($telebotDebugLogger, $telebotUpdatesLogger);
        TelegramLog::$always_log_request_and_response = true;
        $this->allowUsers = explode(',', $allowUsers);
         $this->commands = iterator_to_array($commands);
    }

    public function handle(string $input = null) : ServerResponse {
        $update = $this->createUpdate($input);
        $userId = $update->getMessage()->getFrom()->getId();

        if (!in_array($userId, $this->allowUsers)) {
            throw new \InvalidArgumentException('I\'m sorry, who are you? I do not know you');
        }

        $conversation = $this->getConversation($update);

        if (!$conversation) {
            return TelegramWebDriver::emptyResponse();
        }

        $conversation->addMessageInHistory($update->getMessage());
        $this->entityManager->persist($conversation);
        $this->entityManager->flush();
        $commandName = $conversation->getCommand();

        if (!$commandName) {
            return TelegramWebDriver::emptyResponse();
        }

        /**
         * @var ConversationCommand $command
         */
        if (empty($command = $this->commands[$commandName])) {
            throw new \InvalidArgumentException("The command {$commandName} not configured!");
        }

        $response = $command->execute($conversation, $update);

        if ($response->isOk()) {
            $message = $response->getResult();
            $conversation->addMessageInHistory($message);
            $this->entityManager->persist($conversation);
            $this->entityManager->flush();
        }

        return $response;
    }

    private function getConversation(Update $update) {
        $chatId = $update->getMessage()->getChat()->getId();
        $updateId = $update->getUpdateId();
        $userId = $update->getMessage()->getFrom()->getId();
        $conversation = $this->conversationRepository->findOneBy(['userId' => $userId, 'chatId' => $chatId, 'status' => Conversation::STATUS_OPENED]);

        if ($conversation && $conversation->getLastUpdateId() === $updateId) {
            return null;
        } elseif ($conversation && $conversation->getLastUpdateId() !== $updateId && !$update->getMessage()->getCommand()) {
            $conversation->setLastUpdateId($updateId);
        } else {
            $commandName = $update->getMessage()->getCommand();
            $conversation = (new Conversation())
                ->setUserId($userId)
                ->setChatId($chatId)
                ->setStatus(Conversation::STATUS_OPENED)
                ->setCommand($commandName)
                ->setLastModify(new \DateTime())
                ->setLastUpdateId($updateId);
        }

        return $conversation;
    }

    private function createUpdate(string $input = null) : Update {

        if (!$input) {
            $input = file_get_contents('php://input');
        }

        if (!is_string($input)) {
            throw new \InvalidArgumentException('Input must be a string!');
        }

        if (empty($input)) {
            throw new \InvalidArgumentException('Input is empty!');
        }

        $post = json_decode($input, true);

        return new Update($post, $this->botName);
    }
}