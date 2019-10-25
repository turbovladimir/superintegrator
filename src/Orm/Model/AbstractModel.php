<?php
/**
 * Created by PhpStorm.
 * User: v.sadovnikov
 * Date: 25.10.2019
 * Time: 17:58
 */

namespace App\Orm\Model;

use Doctrine\ORM\EntityManagerInterface;

abstract class AbstractModel
{
    protected $entityManager;
    
    /**
     * Message constructor.
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    
    /**
     *
     */
    public function applyChanges()
    {
        $this->entityManager->flush();
    }
}