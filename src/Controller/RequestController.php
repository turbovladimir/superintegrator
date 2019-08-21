<?php
/**
 * Created by PhpStorm.
 * User: v.sadovnikov
 * Date: 21.08.2019
 * Time: 14:22
 */

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use \App\Services\GeoSearchService;
use \App\Services\AliOrdersService;
use Doctrine\ORM\EntityManagerInterface;

class RequestController extends AbstractController
{
    const MANDATORY_REQUEST_PARAMETERS = ['tool', 'parameters'];
    const GEO_TOOL = 'geo';
    const ALI_ORDERS_TOOL = 'ali_orders';
    
    private $requestData;
    
    public function handle(EntityManagerInterface $entityManager)
    {
        $responseService = new Response(
            '',
            Response::HTTP_OK,
            ['content-type' => 'text/html']
        );
        
        if (empty($_POST['data'])) {
            $responseService->setStatusCode(Response::HTTP_BAD_REQUEST)->send();
            exit();
        }
    
        $requestData = $_POST['data'];
        $requestData = json_decode($requestData, true);
    
        foreach (self::MANDATORY_REQUEST_PARAMETERS as $parameter) {
            if (!array_key_exists($parameter, $requestData)) {
                $responseService->setStatusCode(Response::HTTP_NOT_ACCEPTABLE)->send();
                exit();
            }
        }
        
        $this->requestData = $requestData;
        $this->useService($entityManager);
    }
    
    private function useService(EntityManagerInterface $entityManager)
    {
        $toolName = $this->requestData['tool'];
        $parameters = $this->requestData['parameters'];
        switch ($toolName) {
            case self::GEO_TOOL:
                GeoSearchService::sendResponse($entityManager, $parameters);
                break;
            case self::ALI_ORDERS_TOOL:
                AliOrdersService::sendResponse($parameters);
                break;
        }
    }
}