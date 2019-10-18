<?php
/**
 * Created by PhpStorm.
 * User: v.sadovnikov
 * Date: 18.10.2019
 * Time: 10:33
 */

namespace App\Services\Sender;
use \GuzzleHttp\Client;
use \GuzzleHttp\Exception\GuzzleException;
use \App\Services\AbstractService;
use \App\Services\TaskServiceInterface;
use \App\Entity\Message;

class Sender extends AbstractService implements TaskServiceInterface
{
    public const DEFAULT_LIMIT = 50;
    
    /**
     * @return mixed|void
     */
    public function start()
    {
        $this->send(self::DEFAULT_LIMIT);
    }
    
    /**
     * @param $limit
     */
    public function send($limit)
    {
        $client = new Client();
        $repository = $this->entityManager->getRepository(Message::class);
        $messages = $repository->findBy(['sended' => 0, 'error_text' =>''], [], $limit);
        
        if (empty($messages)) {
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
                $messageEntity->setHasError();
                $messageEntity->setErrorText($e->getMessage());
                sleep(1);
                continue;
            }
        }
    
        $this->entityManager->flush();
    }
}