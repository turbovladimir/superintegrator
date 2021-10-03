<?php

namespace App\Commands\TeleBot\Conversation;

Use App\Services\TeleBot\Exception\ChatWarning;
use App\Entity\Conversation;
use App\Entity\TelebotKey;
use App\Repository\TelebotKeyRepository;
use App\Services\TeleBot\Event\SendMessageEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Tools\Cryptor;

class MyKeysCommand extends ConversationCommand
{
    private $actionsPatterns = [
        'get' => '#(?P<action>\w+)\s(?P<salt>\d+)\s(?P<key>.+)#',
        'get_all' => '#(?P<action>\w+)\s(?P<salt>\d+)#',
        'save' => '#(?P<action>\w+)\s(?P<salt>\d+)\s(?P<key>.+)\s(?P<value>.+)#',
        'delete' => '#(?P<action>\w+)\s(?P<key>.+)#',
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

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(
        EventDispatcherInterface $dispatcher,
        TelebotKeyRepository $keyRepo,
        EntityManagerInterface $entityManager
    ) {
        $this->keyRepo = $keyRepo;
        $this->cruptor = new Cryptor();
        parent::__construct($dispatcher);
        $this->entityManager = $entityManager;
    }


    public function execute(Conversation $conversation) : void {
        $text = $conversation->getLastMessage()->getText(true);

        if (empty($text)) {
            $this->dispatcher->dispatch(new SendMessageEvent($conversation,
                sprintf('Choose what you want? %s', implode(' or ', array_keys($this->actionsPatterns)))));
        }

        preg_match('#(?P<action>\w+).*#', $text, $matches);

        if (empty($matches['action']) || !in_array($matches['action'], array_keys($this->actionsPatterns), true)) {
            throw new ChatWarning('Undefined action message!');
        }

        $action = $matches['action'];
        $userId = $conversation->getLastMessage()->getFrom()->getId();
        preg_match($this->actionsPatterns[$action], $text, $matches);

        if (empty($matches)) {
            throw new ChatWarning("Incorrect parameters for pattern of action `{$action}`");
        }

        $response = '';

        if ($action === 'save') {
            $this->save($userId, $matches['salt'], $matches['key'], $matches['value']);
            $response = 'Save completed!';
        } elseif ($action === 'delete') {
            $this->delete($userId, $matches['key']);
            $response = 'Delete completed!';
        } elseif ($action === 'get') {
            $response = $this->findValueByKey($userId, $matches['salt'], $matches['key']);
        } elseif ($action === 'get_all') {
            $response = $this->findAll($userId, $matches['salt']);
        }

        $this->dispatcher->dispatch(new SendMessageEvent($conversation, $response));
    }

    private function save(int $userId, string $salt,string $key, string $value) {
        $value = $this->cruptor->encrypt($value, $salt);
        $this->entityManager->persist((new TelebotKey())->setUserId($userId)->setName($key)->setValue($value)->setAddedAt(new \DateTime()));
        $this->entityManager->flush();
    }

    private function delete(int $userId, string $name) {
        if (empty($key = $this->keyRepo->findOneBy(['name' => $name, 'userId' => $userId]))) {
            throw new ChatWarning("Cant delete kay: not found by name {$name}");
        }

        $this->entityManager->remove($key);
        $this->entityManager->flush();
    }

    private function findValueByKey(int $userId, string $salt, string $nameLike) : string {
        if (empty($key = $this->keyRepo->fetchKeyByUserIdAndServiceName($userId, $nameLike))) {
            throw new ChatWarning("Key not found by name {$nameLike}");
        }

        return $this->cruptor->decrypt($key->getValue(), $salt);
    }

    private function findAll(int $userId, string $salt)  {
        $keys = $this->keyRepo->findBy(['userId' => $userId]);
        $report = '';

        foreach ($keys as $key) {
            $report .= "**{$key->getName()}**: {$this->cruptor->decrypt($key->getValue(), $salt)}" .PHP_EOL;
        }

        return $report;
    }
}
