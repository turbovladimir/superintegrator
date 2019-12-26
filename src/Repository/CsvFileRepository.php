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
}
