<?php
/**
 * Created by PhpStorm.
 * User: v.sadovnikov
 * Date: 18.10.2019
 * Time: 10:46
 */

namespace App\Entity;

use Doctrine\ORM\EntityManagerInterface;

abstract class BaseEntity implements EntityInterface
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;
    
    /**
     * @var \Doctrine\Common\Persistence\ObjectRepository
     */
    protected $repository;
    
    /**
     * BaseEntity constructor.
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->repository = $this->entityManager->getRepository(self::class);
    }
    
    /**
     * @return object[]
     */
    public function getAll()
    {
        return $this->repository->findAll();
    }
    
    /**
     * @param array $filter
     *
     * @return object|null
     */
    public function getOne($filter)
    {
        return $this->repository->findOneBy($filter);
    }
    
    /**
     *
     */
    public function save()
    {
        $this->entityManager->persist($this);
        $this->entityManager->flush();
    }
    
    /**
     * @param $sql
     *
     * @return mixed
     */
    public function executeQuery($sql)
    {
        $query = $this->entityManager->createQuery($sql);
        return $query->execute();
    }
}