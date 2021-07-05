<?php

namespace App\Commands\TeleBot\Conversation;

use App\Entity\TelebotKey;
use App\Repository\TelebotKeyRepository;
use Doctrine\ORM\EntityManager;
use Longman\TelegramBot\Entities\Message;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Request;

class MyKeysCommand extends ConversationTask
{
    /**
     * @var TelebotKeyRepository
     */
    private $keyRepo;

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var string
     */
    protected $description = 'Store key for accessing to some service';

    public function __construct(TelebotKeyRepository $keyRepo, EntityManager $entityManager) {
        $this->keyRepo = $keyRepo;
        $this->entityManager = $entityManager;
    }


    protected function executeCommand(Message $message): ServerResponse {
        $text = $this->getTextMessage();
        $state = $this->getCurrentState();

        if ($state === 0) {
            $buttons = ['save', 'delete', 'get'];

            if (empty($text) || !in_array($text, $buttons , true)) {
                $serverResponse = $this->createChooseResponse($buttons);
            }

            $this->saveAnswer($text);
            $this->stateUp();
        } elseif ($state === 1) {
            if ($this->fetchAnswer() === 'save') {
                $serverResponse = $this->save($text);
            } elseif ($this->fetchAnswer() === 'delete') {
                $serverResponse = $this->delete($text);
            } elseif ($this->fetchAnswer() === 'get') {
                $serverResponse = $this->findValueByKey($text);
            }

            $this->conversationStop();
        }

        return $serverResponse;
    }

    private function save(string $text) : ServerResponse {
        if (empty($pair = explode(' ', $text))) {
            throw new \InvalidArgumentException('For saving you have to send pair with key and value like "avito mypass1234"');
        }

        $this->entityManager->persist((new TelebotKey())->setName($pair[0])->setValue($pair[1]));
        $this->entityManager->flush();

        return Request::sendMessage($this->createResponseData('Saved complete my master!'));
    }

    private function delete(string $text) : ServerResponse {
        if (empty($key = $this->keyRepo->findOneBy(['name' => $text]))) {
            throw new \InvalidArgumentException("Key not found by name {$text}");
        }

        $this->entityManager->remove($key);
        $this->entityManager->flush();

        return Request::sendMessage($this->createResponseData('Deletion complete my master!'));
    }

    private function findValueByKey(string $text) : ServerResponse {
        if (empty($key = $this->keyRepo->findOneBy(['name' => $text]))) {
            throw new \InvalidArgumentException("Key not found by name {$text}");
        }

        return Request::sendMessage($this->createResponseData($key->getValue()));
    }
}
