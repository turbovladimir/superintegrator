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

    // /**
    //  * @return TelebotKey[] Returns an array of TelebotKey objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?TelebotKey
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
