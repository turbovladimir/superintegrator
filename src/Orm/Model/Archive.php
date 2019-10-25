<?php
/**
 * Created by PhpStorm.
 * User: v.sadovnikov
 * Date: 25.10.2019
 * Time: 17:58
 */

namespace App\Orm\Model;

use App\Orm\Entity\Archive as ArchiveEntity;

class Archive extends AbstractModel
{
    protected $table = 'archives';
    
    /**
     * @param string $sourceName
     * @param array $logs
     *
     * @throws \Exception
     */
    public function saveLog($sourceName, $logs)
    {
        foreach ($logs as $log) {
            $archive = new ArchiveEntity();
            $archive->setSource($sourceName);
            $archive->setLogData($log);
            $this->entityManager->persist($archive);
        }
    
        $this->applyChanges();
    }
}