<?php

namespace App\Repository;

use App\Entity\CsvFile;

/**
 * @method CsvFile|null find($id, $lockMode = null, $lockVersion = null)
 * @method CsvFile|null findOneBy(array $criteria, array $orderBy = null)
 * @method CsvFile[]    findAll()
 * @method CsvFile[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CsvFileRepository extends BaseRepository
{
     protected $entity = CsvFile::class;
    
    /**
     * @param $name
     *
     * @return mixed
     */
     public function getByEstimatedFileName($name)
     {
         $query = $this->_em->createQuery('SELECT c FROM ' . CsvFile::class . ' c WHERE c.fileName LIKE :file_name');
         
         $query->setParameter('file_name', "%{$name}%");
         
         return  $query->getResult();
     }
}
