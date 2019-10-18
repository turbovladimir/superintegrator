<?php
/**
 * Created by PhpStorm.
 * User: v.sadovnikov
 * Date: 18.10.2019
 * Time: 10:33
 */

namespace App\Services\Sender;
use App\Entity\Message;
use \GuzzleHttp\Client;
use \GuzzleHttp\Exception\GuzzleException;
use \App\Services\AbstractService;
use \App\Services\TaskServiceInterface;

class Sender extends AbstractService implements TaskServiceInterface
{
    public const DEFAULT_SEND_PER_TASK = 50;
    public const DEFAULT_DELETE_PER_TASK = 50;
    public const NUMBER_OF_ATTEMPTS = 3;
    
    /**
     * @return mixed|void
     */
    public function start()
    {
        $this->send(self::DEFAULT_SEND_PER_TASK);
        $this->clear(self::DEFAULT_DELETE_PER_TASK);
    }
    
    /**
     * @param $sendingPerTask
     */
    public function send($sendingPerTask)
    {
        $client = new Client();
        $messages = $this->entityManager->createQuery('select * from messages where sended = 0 and attempts <= ' . self::NUMBER_OF_ATTEMPTS . ' limit ' . $sendingPerTask);
        if (!$messages) {
            return;
        }
        
        foreach ($messages as $messageEntity) {
            try {
                $message = json_decode($messageEntity->getMessage(), true);
                $response = $client->request($message['method'], $message['url']);
        
                if ($response->getStatusCode() === 200) {
                    $messageEntity->setSended();
                }
            } catch (GuzzleException $e) {
                $attempts = $messageEntity->getAttempts();
                $messageEntity->setAttempts($attempts++);
                $messageEntity->setErrorText($e->getMessage());
                sleep(1);
                continue;
            }
        }
    
        $this->entityManager->flush();
    }
    
    /**
     * @param $deletingPerTask
     */
    public function clear($deletingPerTask)
    {
        $sendedUrls = $this->entityManager->getRepository(Message::class)->findBy(['sended' => 1], [], $deletingPerTask);
        
        if (empty($sendedUrls)) {
            return;
        }
    
        foreach ($sendedUrls as $urlEntity) {
            $this->entityManager->remove($urlEntity);
        }
        
        $this->entityManager->flush();
    }
}