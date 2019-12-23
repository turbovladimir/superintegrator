<?php

namespace App\Repository;

use App\Entity\Message;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Message|null find($id, $lockMode = null, $lockVersion = null)
 * @method Message|null findOneBy(array $criteria, array $orderBy = null)
 * @method Message[]    findAll()
 * @method Message[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MessageRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Message::class);
    }
    
    public function getEntityManager()
    {
        return parent::getEntityManager();
    }
    
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
            $message = new Message($destination, $url, $headers, $method);
            $this->_em->persist($message);
        }
        
        $this->_em->flush();
    }
    
    /**
     * @param $deletingCount
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function deleteSendedMessage($deletingCount)
    {
        $sendedUrls = $this->_em->getRepository(Message::class)->findBy(['sended' => 1], [], $deletingCount);
        
        if (empty($sendedUrls)) {
            return;
        }
        
        foreach ($sendedUrls as $urlEntity) {
            $this->_em->remove($urlEntity);
        }
        
        $this->_em->flush();
    }

    // /**
    //  * @return Message[] Returns an array of Message objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Message
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
