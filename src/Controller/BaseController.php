<?php
/**
 * Created by PhpStorm.
 * User: v.sadovnikov
 * Date: 14.08.2019
 * Time: 17:20
 */

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use \App\Services\XmlEmulatorService;
use Doctrine\ORM\EntityManagerInterface;

class BaseController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    
    /**
     * @param                        $page
     * @param EntityManagerInterface $entityManager
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function main($page, EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        
        if ($page === '/') {
            return $this->render('base.html.twig');
        } elseif ($page === 'xml') {
            return $this->getXmlPage();
        } else {
            return $this->render("{$page}.html.twig");
        }
    }
    
    /**
     * @param EntityManagerInterface $entityManager
     */
    public function getXmlPage()
    {
        $key = !empty($_GET['key']) ? $_GET['key'] : null;
        
        if ($key === null) {
            //todo доделать
            exit();
        }
        
        return XmlEmulatorService::getXmlPageByKey($this->entityManager, $key);
    }
}