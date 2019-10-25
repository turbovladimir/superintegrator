<?php
/**
 * Created by PhpStorm.
 * User: v.sadovnikov
 * Date: 21.10.2019
 * Time: 13:06
 */

namespace App\Orm\Model;

use Doctrine\ORM\EntityManagerInterface;
use App\Orm\Entity\Message as MessageEntity;

class Message
{
    protected $table = 'messages';
    protected $entityManager;
    
    /**
     * Message constructor.
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    
    /**
     * @param $limit
     * @param $attemptsLimit
     *
     * @return MessageEntity
     */
    public function getAwaitingMessage($limit, $attemptsLimit)
    {
        $query = $this->entityManager->createQuery('select m from '. MessageEntity::class .' m where m.sended = 0 and m.attempts <= ?1');
        $query->setParameter(1,  $attemptsLimit);
        $query->setMaxResults($limit);
        
        return $query->getResult();
    }
    
    /**
     * @param $destination
     *
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getAwaitingMessagesCount($destination)
    {
        $query = $this->entityManager->createQuery('SELECT COUNT(m) FROM ' . MessageEntity::class . ' m WHERE m.sended = 0 AND m.destination = ?1');
        $query->setParameter(1, $destination);
        return $query->getSingleScalarResult();
    }
    
    /**
     * @param        $destination
     * @param        $urls
     * @param null   $headers
     * @param string $method
     *
     * @throws \Exception
     */
    public function saveMessages($destination, $urls, $headers = null, $method = 'GET')
    {
        foreach ($urls as $url) {
            $message = new MessageEntity($destination, $url, $headers, $method);
            $this->entityManager->persist($message);
        }
    
        $this->applyChanges();
    }
    
    /**
     * @param $deletingCount
     */
    public function deleteSendedMessage($deletingCount)
    {
        $sendedUrls = $this->entityManager->getRepository(MessageEntity::class)->findBy(['sended' => 1], [], $deletingCount);
    
        if (empty($sendedUrls)) {
            return;
        }
    
        foreach ($sendedUrls as $urlEntity) {
            $this->entityManager->remove($urlEntity);
        }
    
        $this->applyChanges();
    }
    
    /**
     *
     */
    public function applyChanges()
    {
        $this->entityManager->flush();
    }
}