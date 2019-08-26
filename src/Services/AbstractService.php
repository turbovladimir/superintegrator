<?php
/**
 * Created by PhpStorm.
 * User: v.sadovnikov
 * Date: 26.08.2019
 * Time: 14:13
 */

namespace App\Services;

use Doctrine\ORM\EntityManagerInterface;

abstract class AbstractService
{
    const FILE_SERVICE = false;
    
    protected $entityManager;
    
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    
    abstract public function process($parameters);
    
    /**
     * @return bool
     */
    public function isFileService()
    {
        return self::FILE_SERVICE;
    }
}