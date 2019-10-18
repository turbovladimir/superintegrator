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
    protected $isFileService = false;
    
    protected $entityManager;
    protected $request;
    
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    
    /**
     * @return bool
     */
    public function isFileService()
    {
        return $this->isFileService;
    }
}