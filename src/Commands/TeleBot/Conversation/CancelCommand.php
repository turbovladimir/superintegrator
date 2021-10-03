<?php
namespace App\Commands\TeleBot\Conversation;

use App\Entity\Conversation;
use App\Repository\ConversationRepository;
use App\Services\TeleBot\Event\DeleteMessageEvent;
use App\Services\TeleBot\Event\SendMessageEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class CancelCommand extends ConversationCommand
{
    private $conversationRepository;

    public function __construct(
        EventDispatcherInterface $dispatcher,
        ConversationRepository $conversationRepository)
    {
        $this->conversationRepository = $conversationRepository;
        parent::__construct($dispatcher);
    }

    public function execute(Conversation $conversation) : void {
        $messagesForDeletion = $this->conversationRepository
            ->updateStatus(Conversation::STATUS_OPENED, Conversation::STATUS_CLOSED);

        foreach ($messagesForDeletion as $messageId) {
            $this->dispatcher->dispatch(new DeleteMessageEvent($conversation, $messageId));
        }

        $this->dispatcher->dispatch(new SendMessageEvent($conversation, 'Deletion done!'));
    }
}
