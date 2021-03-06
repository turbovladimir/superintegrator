<?php

namespace App\Repository;

use App\Entity\Message;

/**
 * @method Message|null find($id, $lockMode = null, $lockVersion = null)
 * @method Message|null findOneBy(array $criteria, array $orderBy = null)
 * @method Message[]    findAll()
 * @method Message[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MessageRepository extends BaseRepository
{
    /**
     * @param $limit
     * @param $attemptsLimit
     *
     * @return Message
     */
    public function getAwaitingMessage($limit, $attemptsLimit)
    {
        $query = $this->_em->createQuery('select m from '. Message::class .' m where m.sended = 0 and m.attempts <= ?1');
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
        $query = $this->_em->createQuery('SELECT COUNT(m) FROM ' . Message::class . ' m WHERE m.sended = 0 AND m.destination = ?1');
        $query->setParameter(1, $destination);
        return $query->getSingleScalarResult();
    }
    
    /**
     * @param $deletingCount
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function deleteSendedMessage($deletingCount)
    {
        $this->logger->info('Start deleting sended messages');
        $sentMessages = $this->findBy(['sended' => 1], [], $deletingCount);
        
        if (empty($sentMessages)) {
            $this->logger->info('No messages found');
            return;
        }
    
        $this->logger->info('Found' . count($sentMessages) . ' sent messages. Start deleting...');
        
        foreach ($sentMessages as $message) {
            $this->getEntityManager()->remove($message);
        }
        
        $this->getEntityManager()->flush();
    }
    
    /**
     * @return string
     */
    protected function getEntityName()
    {
        return Message::class;
    }
}
