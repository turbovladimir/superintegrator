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
use \App\Services\Superintegrator\GeoSearchService;
use \App\Services\Superintegrator\AliOrdersService;
use \App\Services\Superintegrator\XmlEmulatorService;
use \App\Services\Superintegrator\PostbackCollector;
use \App\Exceptions\ExpectedException;
use Symfony\Component\HttpFoundation\Request;

class RequestController extends BaseController
{
    
    public function handle()
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
        $service    = $this->useService($toolName, $this->entityManager);
        
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
     *
     * @return AliOrdersService|GeoSearchService|XmlEmulatorService
     */
    private function useService($tool)
    {
        switch ($tool) {
            case self::GEO_TOOL:
                $service = new GeoSearchService($this->entityManager);
                break;
            case self::ALI_ORDERS_TOOL:
                $service = new AliOrdersService($this->entityManager);
                break;
            case self::XML_EMULATOR_TOOL:
                $service = new XmlEmulatorService($this->entityManager);
                break;
            case self::SENDER:
                $service = new PostbackCollector($this->entityManager);
                break;
        }
        
        return $service;
    }
}