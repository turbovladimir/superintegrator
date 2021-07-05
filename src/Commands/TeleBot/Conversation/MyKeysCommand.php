<?php

namespace App\Commands\TeleBot\Conversation;

use App\Entity\TelebotKey;
use App\Repository\TelebotKeyRepository;
use App\Services\TeleBot\TelegramWebDriver;
use Doctrine\ORM\EntityManager;
use Longman\TelegramBot\Entities\Message;
use Longman\TelegramBot\Entities\ServerResponse;

class MyKeysCommand extends ConversationCommand
{
    /**
     * @var TelebotKeyRepository
     */
    private $keyRepo;

    /**
     * @var string
     */
    protected $description = 'Store key for accessing to some service';

    public function __construct(TelebotKeyRepository $keyRepo, EntityManager $entityManager) {
        $this->keyRepo = $keyRepo;
        parent::__construct($entityManager);
    }


    protected function executeCommand(Message $message): ServerResponse {
        $text = $this->getTextMessage();

        if (empty($text)) {
            $data = $this->createResponseData(
                sprintf('Choose what you want? %s', implode(' or ', ['save', 'delete', 'get'])));

            return TelegramWebDriver::sendMessage($data);
        }

        preg_match('#(\w+)\s(\w+)(\s(\w+))?#', $text, $matches);

        if (empty($matches)) {
            throw new \InvalidArgumentException('Incorrect action message!');
        }

        $action = $matches[1];

        if ($action === 'save') {
            if (empty($matches[2]) || empty($matches[4])) {
                throw new \InvalidArgumentException('For saving you have to send pair with key and value like "save avito mypass1234"');
            }

            $serverResponse = $this->save($matches[2], $matches[4]);
        } elseif ($action === 'delete') {
            if (empty($matches[2])) {
                throw new \InvalidArgumentException('For deletion you have to send key like "delete avito"');
            }

            $serverResponse = $this->delete($matches[2]);
        } elseif ($action === 'get') {
            if (empty($matches[2])) {
                throw new \InvalidArgumentException('For fetching you have to send key like "get avito"');
            }
            $serverResponse = $this->findValueByKey($matches[2]);
        }


        return $serverResponse;
    }

    private function save(string $key, string $value): ServerResponse {
        $this->entityManager->persist((new TelebotKey())->setName($key)->setValue($value)->setAddedAt(new \DateTime()));
        $this->entityManager->flush();

        return TelegramWebDriver::sendMessage($this->createResponseData('Saved complete my master!'));
    }

    private function delete(string $name): ServerResponse {
        if (empty($key = $this->keyRepo->findOneBy(['name' => $name]))) {
            throw new \InvalidArgumentException("Key not found by name {$name}");
        }

        $this->entityManager->remove($key);
        $this->entityManager->flush();

        return TelegramWebDriver::sendMessage($this->createResponseData('Deletion complete my master!'));
    }

    private function findValueByKey(string $name): ServerResponse {
        if (empty($key = $this->keyRepo->findOneBy(['name' => $name]))) {
            throw new \InvalidArgumentException("Key not found by name {$name}");
        }

        return TelegramWebDriver::sendMessage($this->createResponseData($key->getValue()));
    }
}
