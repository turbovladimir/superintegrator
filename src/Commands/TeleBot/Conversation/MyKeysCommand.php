<?php

namespace App\Commands\TeleBot\Conversation;

use App\Entity\TelebotKey;
use App\Repository\ConversationRepository;
use App\Repository\TelebotKeyRepository;
use App\Services\TeleBot\TelegramWebDriver;
use Doctrine\ORM\EntityManager;
use Longman\TelegramBot\Entities\Message;
use Longman\TelegramBot\Entities\ServerResponse;
use Tools\Cryptor;

class MyKeysCommand extends ConversationCommand
{
    /**
     * @var TelebotKeyRepository
     */
    private $keyRepo;

    /**
     * @var Cryptor
     */
    private $cruptor;

    /**
     * @var string
     */
    protected $description = 'Store key for accessing to some service';

    public function __construct(
        ConversationRepository $conversationRepository,
        TelebotKeyRepository $keyRepo,
        EntityManager $entityManager
    ) {
        $this->keyRepo = $keyRepo;
        $this->cruptor = new Cryptor();
        parent::__construct($conversationRepository, $entityManager);
    }


    protected function executeCommand(Message $message): ServerResponse {
        $text = $this->getTextMessage();

        if (empty($text)) {
            $data = $this->createResponseData(
                sprintf('Choose what you want? %s', implode(' or ', ['save', 'delete', 'get'])));

            return TelegramWebDriver::sendMessage($data);
        }

        preg_match('#(?P<action>\w+)\s(?P<salt>\w+)\s(?P<key>\w+)(\s(?P<value>\w+))?#', $text, $matches);

        if (empty($matches)) {
            throw new \InvalidArgumentException('Incorrect action message!');
        }

        $action = $matches['action'];
        $userId = $message->getFrom()->getId();

        if ($action === 'save') {
            $this->validate($matches, ['salt', 'key', 'value']);
            try {
                $this->save($userId, $matches['salt'], $matches['key'], $matches['value']);

                return TelegramWebDriver::sendMessage($this->createResponseData('Saved complete my master!'));
            } catch (\Throwable $exception) {
                return TelegramWebDriver::sendMessage($this->createResponseData('Saved fail my master(( Perhaps this key already set!'));
            }
        } elseif ($action === 'delete') {
            $this->validate($matches, ['salt', 'key']);
            $serverResponse = $this->delete($userId, $matches['key']);
        } elseif ($action === 'get') {
            $this->validate($matches, ['salt', 'key']);
            $serverResponse = $this->findValueByKey($userId, $matches['salt'], $matches['key']);
        }


        return $serverResponse;
    }

    private function save(int $userId, string $salt,string $key, string $value) {
        $value = $this->cruptor->encrypt($value, $salt);
        $this->entityManager->persist((new TelebotKey())->setUserId($userId)->setName($key)->setValue($value)->setAddedAt(new \DateTime()));
        $this->entityManager->flush();
    }

    private function delete(int $userId, string $name): ServerResponse {
        if (empty($key = $this->keyRepo->findOneBy(['name' => $name, 'userId' => $userId]))) {
            throw new \InvalidArgumentException("Key not found by name {$name}");
        }

        $this->entityManager->remove($key);
        $this->entityManager->flush();

        return TelegramWebDriver::sendMessage($this->createResponseData('Deletion complete my master!'));
    }

    private function findValueByKey(int $userId, string $salt, string $name): ServerResponse {
        if (empty($key = $this->keyRepo->findOneBy(['name' => $name, 'userId' => $userId]))) {
            throw new \InvalidArgumentException("Key not found by name {$name}");
        }

        $value = $this->cruptor->decrypt($key->getValue(), $salt);

        return TelegramWebDriver::sendMessage($this->createResponseData($value));
    }

    private function validate(array $matches, array $paramNames) {
        foreach ($paramNames as $paramName) {
            if (empty($matches[$paramName])) {
                throw new \InvalidArgumentException(
                    sprintf('Required parameters not set: %s', implode(',', $paramNames)));
            }
        }

    }
}
