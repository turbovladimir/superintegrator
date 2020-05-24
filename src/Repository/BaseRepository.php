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

abstract class BaseRepository extends ServiceEntityRepository
{
    /**
     * @var EntityInterface
     */
    protected $entity;
    
    /**
     * BaseRepository constructor.
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, $this->entity);
    }
    
    /**
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEntityManager()
    {
        return parent::getEntityManager();
    }
}