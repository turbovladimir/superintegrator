<?php


namespace App\Commands\TeleBot\Conversation;

use App\Entity\Conversation;
use App\Services\TeleBot\TelegramWebDriver;
use Doctrine\ORM\EntityManager;
use Longman\TelegramBot\Commands\Command;
use Longman\TelegramBot\Entities\CallbackQuery;
use Longman\TelegramBot\Entities\ChosenInlineResult;
use Longman\TelegramBot\Entities\InlineQuery;
use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Entities\Message;
use Longman\TelegramBot\Entities\Payments\PreCheckoutQuery;
use Longman\TelegramBot\Entities\Payments\ShippingQuery;
use Longman\TelegramBot\Entities\Poll;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Entities\Update;
use Longman\TelegramBot\Exception\TelegramException;

/**
 * Class Command
 *
 * Base class for commands. It includes some helper methods that can fetch data directly from the Update object.
 *
 * @method Message             getMessage()            Optional. New incoming message of any kind — text, photo, sticker, etc.
 * @method Message             getEditedMessage()      Optional. New version of a message that is known to the bot and was edited
 * @method Message             getChannelPost()        Optional. New post in the channel, can be any kind — text, photo, sticker, etc.
 * @method Message             getEditedChannelPost()  Optional. New version of a post in the channel that is known to the bot and was edited
 * @method InlineQuery         getInlineQuery()        Optional. New incoming inline query
 * @method ChosenInlineResult  getChosenInlineResult() Optional. The result of an inline query that was chosen by a user and sent to their chat partner.
 * @method CallbackQuery       getCallbackQuery()      Optional. New incoming callback query
 * @method ShippingQuery       getShippingQuery()      Optional. New incoming shipping query. Only for invoices with flexible price
 * @method PreCheckoutQuery    getPreCheckoutQuery()   Optional. New incoming pre-checkout query. Contains full information about checkout
 * @method Poll                getPoll()               Optional. New poll state. Bots receive only updates about polls, which are sent or stopped by the bot
 */
abstract class ConversationCommand
{
    /**
     * @var Conversation
     */
    private $conversation;

    /**
     * @var Update
     */
    private $update;

    private $name;

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @param Message $message
     * @return ServerResponse
     */
    abstract protected function executeCommand(Message $message) : ServerResponse;

    public function __construct(EntityManager $entityManager) {
        $this->entityManager = $entityManager;
    }

    public function execute(Conversation $conversation, Update $update) {
        $this->update = $update;
        $this->conversation = $conversation;
        $this->entityManager->persist($conversation);
        $this->entityManager->flush();
        $message = $this->getMessage() ?: $this->getEditedMessage();

        try {
            $response = $this->executeCommand($message);
            $this->update = $this->conversation = null;

            return $response;
        } catch (\Throwable $exception) {
            TelegramWebDriver::sendMessage([
                'chat_id' => $message->getChat()->getId(),
                'text' => $exception->getMessage()
            ]);

            throw $exception;
        }
    }

    protected function createChooseResponse(
        array $buttons,
        string $defaultText = 'Choose what you want: '
    ) : ServerResponse {
        $data = $this->createResponseData();
        $data['text'] = $defaultText;
        $data['reply_markup'] = (new Keyboard($buttons))
            ->setResizeKeyboard(true)
            ->setOneTimeKeyboard(true)
            ->setSelective(true);

        return TelegramWebDriver::sendMessage($data);
    }

    protected function createResponseData(string $text = '') : array {
        return [
            'chat_id'      => $this->getMessage()->getChat()->getId(),
            'text'      => $text,
            'reply_markup' => Keyboard::remove(['selective' => true]),
        ];
    }

    protected function getTextMessage() : string {
        return trim($this->getMessage()->getText(true));
    }

    protected function closeConversation() : string {
        $this->conversation->setLastModify(new \DateTime());
        $this->conversation->setStatus(Conversation::STATUS_CLOSED);
        $this->entityManager->persist($this->conversation);
        $this->entityManager->flush();

        return 'Conversation "' .  $this->conversation->getCommand() . '" cancelled!';
    }

    protected function stateUp() {
        $this->conversation->setLastModify(new \DateTime());
        $notes = $this->conversation->getNotes();
        $state = $notes['state'] ?? 0;
        $this->conversation->setNotes(array_merge($notes, ['state' => ++$state]));
        $this->entityManager->persist($this->conversation);
        $this->entityManager->flush();
    }

    protected function fetchAnswer() {
        return $this->conversation->getNotes()['answer'] ?? null;
    }

    protected function saveAnswer(string $answer) {
        $notes = $this->conversation->getNotes();
        $this->conversation->setNotes(array_merge($notes, ['answer' => $answer]));
        $this->entityManager->persist($this->conversation);
        $this->entityManager->flush();
    }

    protected function getCurrentState() {
        $notes = $this->conversation->getNotes();

        return $notes['state'] ?? $notes['state'] = 0;
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

    /**
     * Relay any non-existing function calls to Update object.
     *
     * This is purely a helper method to make TelegramWebDrivers from within execute() method easier.
     *
     * @param string $name
     * @param array  $arguments
     *
     * @return Command
     */
    public function __call($name, array $arguments)
    {
        if ($this->update === null) {
            throw new \BadMethodCallException("Trying to call method non exist method {$name} or update entity was not init");
        }
        return call_user_func_array([$this->update, $name], $arguments);
    }
}