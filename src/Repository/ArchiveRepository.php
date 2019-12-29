<?php

namespace App\Repository;

use App\Entity\Archive;

/**
 * @method Archive|null find($id, $lockMode = null, $lockVersion = null)
 * @method Archive|null findOneBy(array $criteria, array $orderBy = null)
 * @method Archive[]    findAll()
 * @method Archive[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArchiveRepository extends BaseRepository
{
    protected $entity = Archive::class;
    
    /**
     * @param $sourceName
     * @param $logs
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function saveLog($sourceName, $logs)
    {
        foreach ($logs as $log) {
            $archive = new Archive();
            $archive->setSource($sourceName);
            $archive->setLogData($log);
            $this->_em->persist($archive);
        }
        
        $this->_em->flush();
    }
}
