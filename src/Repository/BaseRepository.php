<?php
/**
 * Created by PhpStorm.
 * User: v.sadovnikov
 * Date: 24.12.2019
 * Time: 13:46
 */

namespace App\Repository;


use App\Entity\EntityInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;

abstract class BaseRepository extends ServiceEntityRepository
{
    /**
     * @var EntityInterface
     */
    protected $entity;
    
    /**
     * @var LoggerInterface
     */
    protected $logger;
    
    /**
     * BaseRepository constructor.
     *
     * @param ManagerRegistry $registry
     * @param LoggerInterface $logger
     */
    public function __construct(ManagerRegistry $registry, LoggerInterface $logger)
    {
        $this->logger = $logger;
        parent::__construct($registry, $this->getEntityName());
    }
    
    /**
     * @param EntityInterface $entity
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function save(EntityInterface $entity)
    {
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();
    }
}