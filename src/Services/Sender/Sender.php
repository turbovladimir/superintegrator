<?php
/**
 * Created by PhpStorm.
 * User: v.sadovnikov
 * Date: 18.10.2019
 * Time: 10:33
 */

namespace App\Services\Sender;
use App\Exceptions\EmptyDataException;
use \GuzzleHttp\Client;
use \GuzzleHttp\Exception\GuzzleException;
use \App\Services\TaskServiceInterface;
use \App\Orm\Model\Message;

/**
 * Class Sender
 *
 * @package App\Services\Sender
 */
class Sender implements TaskServiceInterface
{
    public const DEFAULT_SEND_PER_TASK = 50;
    public const DEFAULT_DELETE_PER_TASK = 50;
    public const NUMBER_OF_ATTEMPTS = 3;
    
    /**
     * @var Message
     */
    private $messageModel;
    
    /**
     * Sender constructor.
     *
     * @param Message $messageModel
     */
    public function __construct(Message $messageModel)
    {
        $this->messageModel = $messageModel;
    }
    
    /**
     * @return mixed|void
     */
    public function start()
    {
        $this->send(self::DEFAULT_SEND_PER_TASK);
        $this->messageModel->deleteSendedMessage(self::DEFAULT_DELETE_PER_TASK);
    }
    
    /**
     * @param $sendingPerTask
     *
     * @throws EmptyDataException
     */
    public function send($sendingPerTask)
    {
        $client = new Client();
        $messages = $this->messageModel->getAwaitingMessage($sendingPerTask, self::NUMBER_OF_ATTEMPTS);
        
        if (empty($messages)) {
            throw new EmptyDataException('Nothing to send');
        }
        
        foreach ($messages as $message) {
            try {
                $response = $client->request($message->getMethod(), $message->getUrl());
        
                if ($response->getStatusCode() === 200) {
                    $message->setSended();
                }
            } catch (GuzzleException $e) {
                $attempts = $message->getAttempts();
                $message->setAttempts($attempts++);
                $message->setErrorText($e->getMessage());
                sleep(1);
                continue;
            }
        }
    
        $this->messageModel->applyChanges();
    }
}