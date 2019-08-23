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
use \App\Services\XmlEmulatorService;
use Doctrine\ORM\EntityManagerInterface;

class RequestController extends AbstractController
{
    const MANDATORY_REQUEST_PARAMETERS = ['tool', 'parameters'];
    const GEO_TOOL = 'geo';
    const ALI_ORDERS_TOOL = 'ali_orders';
    const XML_EMULATOR_TOOL = 'xml_emulator';
    
    public function handle(EntityManagerInterface $entityManager)
    {
        $responseService = new Response(
            '',
            Response::HTTP_OK,
            ['content-type' => 'text/html']
        );
        
        if (empty($_POST['data'])) {
            return $responseService->setStatusCode(Response::HTTP_BAD_REQUEST);
        }
    
        $requestData = $_POST['data'];
        $requestData = json_decode($requestData, true);
    
        foreach (self::MANDATORY_REQUEST_PARAMETERS as $parameter) {
            if (!array_key_exists($parameter, $requestData)) {
                return $responseService->setStatusCode(Response::HTTP_NOT_ACCEPTABLE);
            }
        }
        
        $this->useService($entityManager, $requestData, $responseService);
    }
    
    private function useService(EntityManagerInterface $entityManager, $requestData, $responseService)
    {
        $toolName = $requestData['tool'];
        $parameters = $requestData['parameters'];
        switch ($toolName) {
            case self::GEO_TOOL:
                $response = GeoSearchService::sendResponse($entityManager, $parameters);
                break;
            case self::ALI_ORDERS_TOOL:
                $response = AliOrdersService::sendResponse($parameters);
                break;
            case self::XML_EMULATOR_TOOL:
                $response = XmlEmulatorService::sendResponse($entityManager, $parameters);
                break;
        }
        
        return $response;
    }
}