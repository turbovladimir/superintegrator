<?php


namespace App\Commands\TeleBot\Conversation;


use Longman\TelegramBot\Commands\Command;
use Longman\TelegramBot\Conversation;
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
use Longman\TelegramBot\Request;

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
abstract class ConversationTask
{
    /**
     * @var bool
     */
    protected $need_mysql = true;

    /**
     * @var Conversation
     */
    private $conversation;

    /**
     * @var Update
     */
    private $update;

    /**
     * @param Message $message
     * @return ServerResponse
     */
    abstract protected function executeCommand(Message $message) : ServerResponse;

    public function isEnabled() : bool {
        return true;
    }

    public function getUsage() : string {
        return 'some usage';
    }

    /**
     * @param Update $update
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function execute(Update $update) {
        $this->update = $update;
        $message = $this->getMessage() ?: $this->getEditedMessage();
        $chatId = $message->getChat()->getId();
        $this->conversation = new Conversation(
            $message->getFrom()->getId(), $chatId, $this->getName());
        !is_array($this->conversation->notes) && $this->conversation->notes = [];

        try {
            $this->executeCommand($message);
        } catch (\Throwable $exception) {
            Request::sendMessage([
                'chat_id' => $chatId,
                'text' => $exception->getMessage()
            ]);

            throw $exception;
        }

        $this->update = null;
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

        return Request::sendMessage($data);
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

    protected function conversationStop() {
        $this->conversation->stop();
    }

    protected function stateUp() {
        ++$this->conversation->notes['state'];
        $this->conversation->update();
    }

    protected function fetchAnswer() {
        return $this->conversation->notes['answer'] ?? null;
    }

    protected function saveAnswer(string $answer) {
        $this->conversation->notes['answer'] = $answer;
        $this->conversation->update();
    }

    protected function getCurrentState() {
        return $this->conversation->notes['state'] ?? $this->conversation->notes['state'] = 0;
    }

    /**
     * Relay any non-existing function calls to Update object.
     *
     * This is purely a helper method to make requests from within execute() method easier.
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