<?php
/**
 * Created by PhpStorm.
 * User: v.sadovnikov
 * Date: 21.08.2019
 * Time: 14:22
 */

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use \App\Services\GeoSearchService;
use \App\Services\AliOrdersService;
use \App\Services\XmlEmulatorService;
use \App\Exceptions\ExpectedException;
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
        
        $toolName   = $requestData['tool'];
        $parameters = $requestData['parameters'];
        $service    = $this->useService($toolName, $entityManager);
        
        try {
            $processedData = $service->process($parameters);
            
            if ($service->isFileService()) {
                $disposition = $responseService->headers->makeDisposition(
                    ResponseHeaderBag::DISPOSITION_ATTACHMENT,
                    $service->fileName
                );
                $responseService->headers->set('Content-Disposition', $disposition);
            }
            
            $responseService->setContent($processedData);
            
            } catch (ExpectedException $e) {
            $responseService->setContent($e->getMessage());
            $responseService->setStatusCode(403);
            }
        
        return $responseService;
    }
    
    /**
     * @param $tool
     * @param $entityManager
     *
     * @return AliOrdersService|GeoSearchService|XmlEmulatorService
     */
    private function useService($tool, $entityManager)
    {
        switch ($tool) {
            case self::GEO_TOOL:
                $service = new GeoSearchService($entityManager);
                break;
            case self::ALI_ORDERS_TOOL:
                $service = new AliOrdersService($entityManager);
                break;
            case self::XML_EMULATOR_TOOL:
                $service = new XmlEmulatorService($entityManager);
                break;
        }
        
        return $service;
    }
}