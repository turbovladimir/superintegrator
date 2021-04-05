<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * @param string $name
     * @return User|null
     */
    public function findByName(string $name) {
        return $this->findOneBy(['name' => $name]);
    }

    /**
     * @param string $hash
     * @return User|null
     */
    public function findByHash(string $hash) {
        return $this->findOneBy(['password' => $hash]);
    }
}
