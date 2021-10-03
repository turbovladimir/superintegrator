<?php


namespace App\Commands\TeleBot\Conversation;

Use App\Services\TeleBot\Exception\ChatWarning;
use App\Entity\Conversation;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;


abstract class ConversationCommand
{
    private $name;

    /**
     * @var EventDispatcherInterface
     */
    protected $dispatcher;


    /**
     * @param Conversation $conversation
     * @return void
     * @throws ChatWarning
     */
    abstract public function execute(Conversation $conversation) : void;

    public function __construct(EventDispatcherInterface $dispatcher) {
        $this->dispatcher = $dispatcher;
    }

    /**
     * @return mixed
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name): void {
        $this->name = $name;
    }
}