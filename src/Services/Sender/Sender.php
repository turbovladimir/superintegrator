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
        $query = $this->entityManager->createQuery('select m from '. Message::class .' m where m.sended = 0 and m.attempts <= ?1');
        $query->setParameter(1,  self::NUMBER_OF_ATTEMPTS);
        $query->setMaxResults($sendingPerTask);
        $messages = $query->getResult();
        if (!$messages && empty($messages)) {
            return;
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