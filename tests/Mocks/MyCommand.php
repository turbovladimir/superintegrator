<?php

namespace App\Tests\Mocks;

use App\Commands\TeleBot\Conversation\ConversationCommand;
use App\Entity\Conversation;

class MyCommand extends ConversationCommand
{
    public function execute(Conversation $conversation): void
    {
        // TODO: Implement execute() method.
    }
}