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
    private $actionsPatterns = [
        'get' => '#(?P<action>\w+)\s(?P<salt>\w+)\s(?P<key>\w+)#',
        'get_all' => '#(?P<action>\w+)\s(?P<salt>\w+)#',
        'save' => '#(?P<action>\w+)\s(?P<salt>\w+)\s(?P<key>\w+)\s(?P<value>\w+)#',
        'delete' => '#(?P<action>\w+)\s(?P<key>\w+)#',
    ];

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
                sprintf('Choose what you want? %s', implode(' or ', array_keys($this->actionsPatterns))));

            return TelegramWebDriver::sendMessage($data);
        }

        preg_match('#(?P<action>\w+).*#', $text, $matches);

        if (empty($matches['action']) || !in_array($matches['action'], array_keys($this->actionsPatterns), true)) {
            throw new \InvalidArgumentException('Undefined action message!');
        }

        $action = $matches['action'];
        $userId = $message->getFrom()->getId();


        preg_match($this->actionsPatterns[$action], $text, $matches);

        if (empty($matches)) {
            throw new \InvalidArgumentException("Incorrect parameters for pattern of action `{$action}`");
        }

        if ($action === 'save') {
            try {
                $this->save($userId, $matches['salt'], $matches['key'], $matches['value']);

                $serverResponse = TelegramWebDriver::sendMessage($this->createResponseData('Saved complete my master!'));
            } catch (\Throwable $exception) {
                $serverResponse = TelegramWebDriver::sendMessage($this->createResponseData('Saved fail my master(( Perhaps this key already set!'));
            }
        } elseif ($action === 'delete') {
            $serverResponse = $this->delete($userId, $matches['key']);
        } elseif ($action === 'get') {
            $serverResponse = $this->findValueByKey($userId, $matches['salt'], $matches['key']);
        } elseif ($action === 'get_all') {
            $serverResponse = $this->findAll($userId, $matches['salt']);
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

    private function findAll(int $userId, string $salt) : ServerResponse  {
        $keys = $this->keyRepo->findBy(['userId' => $userId]);
        $report = '';

        /**
         * @var TelebotKey $key
         */
        foreach ($keys as $key) {
            $report .= "**{$key->getName()}**: {$this->cruptor->decrypt($key->getValue(), $salt)}" .PHP_EOL;
        }

        return TelegramWebDriver::sendMessage($this->createResponseData($report));
    }
}
