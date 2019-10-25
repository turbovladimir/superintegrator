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
use \App\Services\Superintegrator\GeoSearchService;
use \App\Services\Superintegrator\AliOrdersService;
use \App\Services\Superintegrator\PostbackCollector;

class HttpController extends BaseController
{
    const GEO_PAGE = '/geo';
    const ALI_ORDERS_PAGE = '/ali_orders';
    const XML_EMULATOR_PAGE = '/xml_emulator';
    const SENDER_PAGE = '/sender';
    
    private $request;
    private $requestContent;
    private $geoSearch;
    private $xmlEmulator;
    private $postbackCollector;
    private $aliOrders;
    private $csvHandler;
    
    /**
     * @param Request            $request
     * @param GeoSearchService   $geoSearch
     * @param XmlEmulatorService $xmlEmulator
     * @param PostbackCollector  $postbackCollector
     * @param AliOrdersService   $aliOrders
     * @param CsvHandler         $csvHandler
     *
     * @return Response
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \League\Csv\Exception
     */
    public function index(
        Request $request,
        GeoSearchService $geoSearch,
        XmlEmulatorService $xmlEmulator,
        PostbackCollector $postbackCollector,
        AliOrdersService $aliOrders,
        CsvHandler $csvHandler
    ) {
        $response         = [];
        $this->request           = $request;
        $this->requestContent    = urldecode($this->request->getContent());
        $this->geoSearch         = $geoSearch;
        $this->xmlEmulator       = $xmlEmulator;
        $this->postbackCollector = $postbackCollector;
        $this->aliOrders         = $aliOrders;
        $this->csvHandler        = $csvHandler;
        
        $path = $this->request->getPathInfo();
        $path === '/' ? $path = '/base' : null;
        $page = str_replace('/', '', $path);
        
        try {
            if ($this->request->getMethod() === 'GET') {
                switch ($path) {
                    case (self::SENDER_PAGE):
                        return $this->render('sender.html.twig', ['notSendedPostbacks' => $postbackCollector->getAwaitingPostbacks()]);
                    case ('/xml'):
                        return $this->getXmlPage();
                    default:
                        return $this->render("{$page}.html.twig", ['description' => $this->setDescription($path)]);
                }
            } else {
                switch ($path) {
                    case ('/sender'):
                        $response['confirmed'] = $this->csvHandler->uploadFileAction($this->request);
                        break;
                    case (self::GEO_PAGE):
                        $response = $this->geoSearch->process($_POST['geo'] ?? null);
                        break;
                    case (self::ALI_ORDERS_PAGE):
                        return $this->aliOrders->process($_POST['orders'] ?? null);
                    case (self::XML_EMULATOR_PAGE):
                        $response = $this->xmlEmulator->process($this->requestContent);
                        break;
                }
            }
        } catch
        (ExpectedException $expectedException) {
            $response = $expectedException->getMessage();
        }
        
        return $this->render("{$page}.html.twig", ['response' => $response]);
    }
    
    /**
     * @todo  переделать
     *
     * @return Response
     */
    public function getXmlPage()
    {
        $query = parse_query($this->request->getQueryString());
        
        if (empty($query['key'])) {
            //todo доделать
            return (new Response())->setStatusCode(Response::HTTP_BAD_REQUEST);
        }
        
        try {
            $xml = $this->xmlEmulator->getXmlPageByKey($query['key']);
        } catch (ExpectedException $e) {
            return new Response('<error>'.$e->getMessage().'</error>', 403, ['Content-Type' => 'text/xml']);
        }
        
        return new Response($xml, 200, ['Content-Type' => 'text/xml']);
    }
}