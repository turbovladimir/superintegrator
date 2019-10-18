<?php
/**
 * Created by PhpStorm.
 * User: v.sadovnikov
 * Date: 02.09.2019
 * Time: 13:45
 */

namespace App\Controller;

use App\Services\File\CsvHandler;
use \App\Services\Superintegrator\XmlEmulatorService;
use \App\Exceptions\ExpectedException;
use function GuzzleHttp\Psr7\parse_query;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use \App\Services\Superintegrator\GeoSearchService;
use \App\Services\Superintegrator\AliOrdersService;
use \App\Services\Superintegrator\PostbackCollector;

class HttpController extends BaseController
{
    
    /**
     * @param Request  $request
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $path = $request->getPathInfo();
        
        if ($request->getMethod() === 'GET') {
            switch ($path) {
                case ('/'):
                    return $this->render('base.html.twig', ['message' => 'Main page']);
                case ('/xml'):
                    return $this->getXmlPage($request);
                default:
                    $page = substr($path, 1);
                    return $this->render("{$page}.html.twig");
            }
        } else {
            try{
            $responseMessage = '';
            
            if ($path === '/fileUpload') {
                $files = $request->files->all() ?: false;
                
                if (!$files) {
                    throw new ExpectedException('Cant find files for save');
                }
    
                $files = reset($files);
                $uploader = new CsvHandler($this->entityManager);
                
                foreach ($files as $file) {
                    $uploader->uploadCSV($file);
                }
    
                $responseMessage = 'Files have been successfully added';
            }
    
//            $requestData = $_POST['data'];
//            $requestData = json_decode($requestData, true);
//
//            foreach (self::MANDATORY_REQUEST_PARAMETERS as $parameter) {
//                if (!array_key_exists($parameter, $requestData)) {
//                    return $responseService->setStatusCode(Response::HTTP_NOT_ACCEPTABLE);
//                }
//            }
//
//            $toolName   = $requestData['tool'];
//            $parameters = $requestData['parameters'];
//            $service    = $this->useService($toolName, $this->entityManager);
//
//            try {
//                $processedData = $service->process($parameters);
//
//                if ($service->isFileService()) {
//                    $disposition = $responseService->headers->makeDisposition(
//                        ResponseHeaderBag::DISPOSITION_ATTACHMENT,
//                        $service->fileName
//                    );
//                    $responseService->headers->set('Content-Disposition', $disposition);
//                }
//
//                $responseService->setContent($processedData);
//
//            } catch (ExpectedException $e) {
//                $responseService->setContent($e->getMessage());
//                $responseService->setStatusCode(403);
//            }
//
//            return $responseService;
            } catch (ExpectedException $expectedException) {
                $responseMessage = $expectedException->getMessage();
            }
    
            return $this->render('base.html.twig', ['message' => $responseMessage]);
       }
    }
    
    /**
     * @todo  переделать
     *
     * @param Request $request
     *
     * @return Response
     */
    public function getXmlPage(Request $request)
    {
        $query = parse_query($request->getQueryString());
        
        if (empty($query['key'])) {
            //todo доделать
            return (new Response())->setStatusCode(Response::HTTP_BAD_REQUEST);
        }
        
        $service = new XmlEmulatorService($this->entityManager);
        
        try {
            $xml = $service->getXmlPageByKey($query['key']);
        } catch (ExpectedException $e) {
            return new Response('<error>'.$e->getMessage().'</error>', 403, ['Content-Type' => 'text/xml']);
        }
        
        return new Response($xml, 200, ['Content-Type' => 'text/xml']);
    }
}