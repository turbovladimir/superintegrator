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
use App\Repository\MessageRepository;

/**
 * Class Sender
 *
 * @package App\Services\Sender
 */
class MessageSender implements TaskServiceInterface
{
    public const DEFAULT_SEND_PER_TASK = 50;
    public const DEFAULT_DELETE_PER_TASK = 50;
    public const NUMBER_OF_ATTEMPTS = 3;
    
    /**
     * @var MessageRepository
     */
    private $messageRepository;
    
    /**
     * Sender constructor.
     *
     * @param MessageRepository $messageModel
     */
    public function __construct(MessageRepository $messageRepository)
    {
        $this->messageRepository = $messageRepository;
    }
    
    /**
     * @return mixed|void
     */
    public function start()
    {
        $this->send(self::DEFAULT_SEND_PER_TASK);
        $this->messageRepository->deleteSendedMessage(self::DEFAULT_DELETE_PER_TASK);
    }
    
    /**
     * @param $sendingPerTask
     *
     * @throws EmptyDataException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function send($sendingPerTask)
    {
        $client = new Client();
        $messages = $this->messageRepository->getAwaitingMessage($sendingPerTask, self::NUMBER_OF_ATTEMPTS);
        
        if (!$messages) {
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
    
        $this->messageRepository->getEntityManager()->flush();
    }
}