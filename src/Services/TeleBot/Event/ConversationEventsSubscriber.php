<?php

namespace App\Services\TeleBot\Event;

use App\Services\TeleBot\Http\Driver;
use App\Services\TeleBot\Log\ConversationDebugProcessor;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ConversationEventsSubscriber implements EventSubscriberInterface
{
    private $entityManager;

    /**
     * @var ConversationDebugProcessor
     */
    private $debugProcessor;
    /**
     * @var Driver
     */
    private $driver;

    public function __construct(
        ConversationDebugProcessor $debugProcessor,
        EntityManagerInterface $entityManager,
        Driver $driver
    ) {
        $this->entityManager = $entityManager;
        $this->debugProcessor = $debugProcessor;
        $this->driver = $driver;
    }


    public static function getSubscribedEvents()
    {
        return [
            ConversationStartEvent::class => 'onConversationStartEvent',
            SendMessageEvent::class => 'onSendDataTelegramEvent',
            SendStickerEvent::class => 'onSendDataTelegramEvent',
            SendKeyboardEvent::class => 'onSendDataTelegramEvent',
        ];
    }

    public function onConversationStartEvent(ConversationStartEvent $event) {
        $this->debugProcessor->init($event->getConversation());
    }

    public function onSendDataTelegramEvent(SendDataTelegramEvent $event) {
        $message = $this->driver->makeRequest($event->getTelegramMethod(), $event->getData());
        $conversation = $event->getConversation();
        $conversation->addMessageInHistory($message->getMessageId(), $message->getText());
        $this->entityManager->persist($conversation);
        $this->entityManager->flush();
    }
}