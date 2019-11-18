<?php
/**
 * Created by PhpStorm.
 * User: v.sadovnikov
 * Date: 02.09.2019
 * Time: 13:45
 */

namespace App\Controller;

use App\Response\AlertMessageCollection;
use App\Response\Download;
use App\Services\File\CsvHandler;
use \App\Services\Superintegrator\XmlEmulatorService;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use \App\Services\Superintegrator\GeoSearchService;
use \App\Services\Superintegrator\AliOrdersService;
use \App\Services\Superintegrator\PostbackCollector;

class HttpController extends AbstractController
{
    
    private const PAGE_MAIN = 'base';
    private const PAGE_GEO = 'geo';
    private const PAGE_ALI_ORDERS = 'ali_orders';
    private const PAGE_XML_EMULATOR = 'xml_emulator';
    private const PAGE_SENDER = 'sender';
    private const PAGE_XML = 'xml';
    
    private const ROUTS_AND_ALIASES = [
        self::PAGE_MAIN         => 'Main page',
        self::PAGE_GEO          => 'Geo searching',
        self::PAGE_ALI_ORDERS   => 'Ali orders',
        self::PAGE_XML_EMULATOR => 'Xml emulator',
        self::PAGE_SENDER       => 'Sender',
    ];
    
    private $request;
    private $requestContent;
    private $geoSearch;
    private $xmlEmulator;
    private $postbackCollector;
    private $aliOrders;
    private $csvHandler;
    private $logger;
    
    /**
     * @param string             $page
     * @param Request            $request
     * @param LoggerInterface    $logger
     * @param GeoSearchService   $geoSearch
     * @param XmlEmulatorService $xmlEmulator
     * @param PostbackCollector  $postbackCollector
     * @param AliOrdersService   $aliOrders
     * @param CsvHandler         $csvHandler
     *
     * @return Response
     */
    public function index(
        string $page,
        Request $request,
        LoggerInterface $logger,
        GeoSearchService $geoSearch,
        XmlEmulatorService $xmlEmulator,
        PostbackCollector $postbackCollector,
        AliOrdersService $aliOrders,
        CsvHandler $csvHandler
    ) {
        $response                = [];
        $this->request           = $request;
        $this->logger            = $logger;
        $this->requestContent    = urldecode($this->request->getContent());
        $this->geoSearch         = $geoSearch;
        $this->xmlEmulator       = $xmlEmulator;
        $this->postbackCollector = $postbackCollector;
        $this->aliOrders         = $aliOrders;
        $this->csvHandler        = $csvHandler;
    
        $page === '/' ? $page = 'base' : null;
        
        try {
            if ($this->request->getMethod() === 'GET') {
                switch ($page) {
                    case (self::PAGE_SENDER):
                        return $this->render('sender.html.twig', ['notSendedPostbacks' => $postbackCollector->getAwaitingPostbacks()]);
                        break;
                    case (self::PAGE_XML_EMULATOR):
                        $collection = $xmlEmulator->getCollection();
                        
                        return $this->render('xml_emulator.html.twig', $collection ? ['table_head' => array_keys(reset($collection)), 'xml_collection' => $collection] : []);
                        break;
                    case (self::PAGE_XML):
                        return $xmlEmulator->getXmlPage($request);
                    default:
                        return $this->render("{$page}.html.twig");
                }
            } else {
                switch ($page) {
                    case ('/sender'):
                        $response['confirmed'] = $this->csvHandler->uploadFileAction($this->request);
                        break;
                    case (self::PAGE_GEO):
                        $response = $this->geoSearch->processRequest($request);
                        break;
                    case (self::PAGE_ALI_ORDERS):
                        $response = $this->aliOrders->processRequest($request);
                        break;
                    case (self::PAGE_XML_EMULATOR):
                        $response = $this->xmlEmulator->processRequest($request);
                        break;
                }
            }
        } catch
        (\Exception $exception) {
            $response = new AlertMessageCollection();
            $response->addAlert('Обнаружена ошибка', $exception->getMessage(), AlertMessageCollection::ALERT_TYPE_DANGER);
        }
    
        if ($response instanceof Download) {
            return  $response->get();
        }
        
        return $this->render("{$page}.html.twig", ['response' => $response->getMessages()]);
    }
    
    /**
     * @param string $page
     *
     * @return Response
     */
    public function createNew(string $page)
    {
        return $this->render("{$page}.html.twig", ['new_form' => 1]);
    }
    
    /**
     * @param $rout
     *
     * @return mixed
     */
    private function getPageName($rout)
    {
        if (array_key_exists($rout, self::ROUTS_AND_ALIASES)) {
            return self::ROUTS_AND_ALIASES[$rout];
        }
        
        return self::ROUTS_AND_ALIASES[self::PAGE_MAIN];
    }
    
    /**
     * @param $path
     *
     * @return string
     */
    protected function setDescription($rout)
    {
        return '';
    }
    
    /**
     * @param string        $view
     * @param array         $parameters
     * @param Response|null $response
     *
     * @return Response
     */
    protected function render(string $view, array $parameters = [], Response $response = null) : Response
    {
        $page = str_replace('.html.twig', '', $view);
        $parameters['page_name'] = $this->getPageName($page);
        $parameters['description'] = $this->setDescription($page);
        
        return parent::render($view, $parameters, $response);
    }
}