<?php
/**
 * Created by PhpStorm.
 * User: v.sadovnikov
 * Date: 18.10.2019
 * Time: 10:33
 */

namespace App\Services\Sender;
use App\Entity\Message;
use App\Exceptions\EmptyDataException;
use App\Services\Tools\CityadsPostbackManager;
use \GuzzleHttp\Client;
use \GuzzleHttp\Exception\GuzzleException;
use App\Repository\MessageRepository;
use Psr\Log\LoggerInterface;

/**
 * Class Sender
 *
 * @package App\Services\Sender
 */
class MessageManager
{
    public const DEFAULT_SEND_PER_TASK = 50;
    public const DEFAULT_DELETE_PER_TASK = 50;
    public const NUMBER_OF_ATTEMPTS = 3;
    
    /**
     * @var MessageRepository
     */
    private $messageRepository;
    private $logger;
    
    /**
     * MessageSender constructor.
     *
     * @param MessageRepository $messageRepository
     * @param LoggerInterface   $logger
     */
    public function __construct(MessageRepository $messageRepository, LoggerInterface $logger)
    {
        $this->messageRepository = $messageRepository;
        $this->logger = $logger;
    }
    
    /**
     * @param        $destination
     * @param array  $url
     * @param null   $headers
     * @param string $method
     *
     * @throws \Exception
     */
    public function saveMessage($destination, $url, $headers = null, $method = 'GET')
    {
        $message = new Message($destination, $url, $headers, $method);
        $this->messageRepository->save($message);
    }
    
    /**
     * @param bool $deleteAfterSending
     * @param int  $sendingPerTask
     *
     * @throws
     */
    public function send($deleteAfterSending = true, $sendingPerTask = self::DEFAULT_SEND_PER_TASK)
    {
        $client = new Client();
        $messages = $this->messageRepository->getAwaitingMessage($sendingPerTask, self::NUMBER_OF_ATTEMPTS);
        
        if (!$messages) {
            throw new EmptyDataException('Nothing to send');
        }
        /**
         * @var $message Message
         */
        foreach ($messages as $message) {
            try {
                $response = $client->request($message->getMethod(), $message->getUrl());
        
                if ($response->getStatusCode() === 200) {
                    $message->setSended();
                    $this->messageRepository->save($message);
                }
            } catch (GuzzleException $e) {
                $attempts = $message->getAttempts();
                $message->setAttempts(++$attempts);
                $message->setErrorText($e->getMessage());
                $this->logger->warning($e->getMessage());
                sleep(1);
                continue;
            }
        }
    
        if ($deleteAfterSending) {
            $this->messageRepository->deleteSendedMessage(self::DEFAULT_DELETE_PER_TASK);
        }
    }
}