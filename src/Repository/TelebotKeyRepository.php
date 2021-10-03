<?php

namespace App\Repository;

use App\Entity\TelebotKey;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TelebotKey|null find($id, $lockMode = null, $lockVersion = null)
 * @method TelebotKey|null findOneBy(array $criteria, array $orderBy = null)
 * @method TelebotKey[]    findAll()
 * @method TelebotKey[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TelebotKeyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TelebotKey::class);
    }

    public function fetchKeyByUserIdAndServiceName(int $userId, string $nameLike) {
        return $this->_em->createQuery('
            SELECT k FROM App\Entity\TelebotKey k WHERE k.userId = :user_id AND k.name LIKE :name_like')
            ->setParameter('user_id', $userId)
            ->setParameter('name_like' , "%{$nameLike}%")
            ->getOneOrNullResult();
    }
}
