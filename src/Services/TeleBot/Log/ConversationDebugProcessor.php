<?php

namespace App\Services\TeleBot\Log;

use App\Entity\Conversation;
use App\Services\TeleBot\Event\SendMessageEvent;
use Monolog\Processor\ProcessorInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ConversationDebugProcessor implements ProcessorInterface
{
    /**
     * @var string
     */
    private $environment;
    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    public function __construct(EventDispatcherInterface $dispatcher, string $environment) {
        $this->environment = $environment;
        $this->dispatcher = $dispatcher;
    }

    /**
     * @var Conversation
     */
    private $conversation;

    public function init(Conversation $conversation) {
        $this->conversation = $conversation;
    }

    public function __invoke(array $records) {
        if ($this->environment === 'dev' && $this->conversation) {
            foreach ($records as $record) {
                $this->dispatcher->dispatch(new SendMessageEvent($this->conversation, $record));
            }
        }

        return $records;
    }
}