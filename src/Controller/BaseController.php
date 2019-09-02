<?php
/**
 * Created by PhpStorm.
 * User: v.sadovnikov
 * Date: 14.08.2019
 * Time: 17:20
 */

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class BaseController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;
    
    /**
     * @var LoggerInterface
     */
    protected $logger;
    
    public function __construct(LoggerInterface $logger, EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->logger = $logger;
    }
}