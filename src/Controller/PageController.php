<?php
/**
 * Created by PhpStorm.
 * User: v.sadovnikov
 * Date: 02.09.2019
 * Time: 13:45
 */

namespace App\Controller;

use \App\Services\XmlEmulatorService;
use \App\Services\OmarsysDataAggregator;
use \App\Exceptions\ExpectedException;
use Symfony\Component\HttpFoundation\Response;

class PageController extends BaseController
{
    public function index($page)
    {
        switch ($page) {
            case ('/'):
                return $this->render('base.html.twig');
            case ('xml'):
                return $this->getXmlPage();
            case ('test-aggregator'):
                return $this->testAgggregator();
            default:
                return $this->render("{$page}.html.twig");
        }
    }
    
    public function getXmlPage()
    {
        $key = !empty($_GET['key']) ? $_GET['key'] : null;
        
        if ($key === null) {
            //todo доделать
            exit();
        }
        $service = new XmlEmulatorService($this->entityManager);
        
        try {
            $xml = $service->getXmlPageByKey($key);
        } catch (ExpectedException $e) {
            return new Response('<error>'.$e->getMessage().'</error>', 403, ['Content-Type' => 'text/xml']);
        }
        
        return new Response($xml, 200, ['Content-Type' => 'text/xml']);
    }
    
    //todo убрать
    public function testAgggregator()
    {
        $service = new OmarsysDataAggregator($this->entityManager);
        $service->process([]);
    }
}