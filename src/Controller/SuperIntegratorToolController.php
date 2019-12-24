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
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use App\Services\Superintegrator\AliOrdersService;
use App\Services\Superintegrator\GeoSearchService;
use App\Services\Superintegrator\PostbackCollector;
use App\Services\Superintegrator\XmlEmulatorService;
use Psr\Log\LoggerInterface;

class SuperIntegratorToolController extends BaseController
{
    public const PAGE_MAIN = 'base';
    public const PAGE_GEO = 'geo';
    public const PAGE_ALI_ORDERS = 'ali_orders';
    public const PAGE_XML_EMULATOR = 'xml_emulator';
    public const PAGE_SENDER = 'sender';
    
    public const PAGE_XML_TYPE = 'xml';
    public const PAGE_JSON_TYPE = 'json';
    
    public const ACTION_NEW = 'new';
    public const ACTION_UPLOAD = 'upload';
    public const ACTION_json = 'get_json';
    
    private const ROUTS_PARAMS = [
        self::PAGE_MAIN         => [
            'title' => 'Main page',
            'description' => 'Добро пожаловать в наш скромный ламповый сервис'
        ],
        self::PAGE_GEO          => [
            'title' => 'Geo searching',
            'description' => 'Инструмент для поиска id гео объектов (страны, регионы, города), для получения id можно использовать как 2х буквенные коды стран так и полные ENG наименования'
        ],
        self::PAGE_ALI_ORDERS   => [
            'title' => 'Ali orders',
            'description' => 'Позволяет забирать подробную информацию о заказах алиэкспресс'
        ],
        self::PAGE_XML_EMULATOR => [
            'title' => 'Xml emulator',
            'description' => 'При помощи данного инструмента вы можете создавать ссылки эмулирующие работу API с форматом ответа xml'
        ],
        self::PAGE_SENDER       => [
            'title' => 'Sender',
            'description' => 'Переотправка постбэков и пикселей по файлам архива админки процессинга'
        ],
        self::PAGE_XML_TYPE       => ['title' => ''],
    ];
    
    private $geoSearch;
    private $xmlEmulator;
    private $postbackCollector;
    private $aliOrders;
    private $logger;
    
    /**
     * HttpController constructor.
     *
     * @param LoggerInterface    $logger
     * @param GeoSearchService   $geoSearch
     * @param XmlEmulatorService $xmlEmulator
     * @param PostbackCollector  $postbackCollector
     * @param AliOrdersService   $aliOrders
     */
    public function __construct(
        LoggerInterface $logger,
        GeoSearchService $geoSearch,
        XmlEmulatorService $xmlEmulator,
        PostbackCollector $postbackCollector,
        AliOrdersService $aliOrders
    ) {
        $this->logger            = $logger;
        $this->geoSearch         = $geoSearch;
        $this->xmlEmulator       = $xmlEmulator;
        $this->postbackCollector = $postbackCollector;
        $this->aliOrders         = $aliOrders;
    }
    
    /**
     * @param         $page
     * @param         $action
     * @param Request $request
     *
     * @return Response
     */
    public function index($tool, $action = null, Request $request)
    {
        if (!array_key_exists($tool, self::ROUTS_PARAMS)) {
            $this->mainPage();
        }
        
        try {
            if ($request->getMethod() === 'POST') {
                return $this->handlePostRequest($request, $tool, $action);
            }
            
            return $this->handleGetRequets($request, $tool, $action);
        } catch (\Exception $exception) {
            $response = new AlertMessageCollection('Обнаружена ошибка', $exception->getMessage(), AlertMessageCollection::ALERT_TYPE_DANGER);
            
            return $this->renderPage($tool, ['response' => $response->getMessages()]);
        }
    }
    
    /**
     * @param Request $request
     * @param         $page
     * @param null    $action
     *
     * @return Response
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    private function handleGetRequets(Request $request, $page, $action = null)
    {
        $options = [];
        
        if ($action === self::ACTION_NEW) {
            $options['new_form'] = 1;
        }
        
        switch ($page) {
            case (self::PAGE_SENDER):
                $options['response'] = $this->postbackCollector->getAwaitingPostbacks();
                break;
            case (self::PAGE_XML_EMULATOR):
                
                if ($action === self::PAGE_XML_TYPE) {
                    $xml = $this->xmlEmulator->getXml($request);
                    
                    return $this->renderXmlPage($xml);
                } else {
                    $options = array_merge($options, $this->xmlEmulator->getTableWithXmlTemplates());
                }
                
                break;
        }
        
        return $this->renderPage($page, $options);
    }
    
    /**
     * @param Request $request
     * @param         $page
     * @param null    $action
     *
     * @return Response
     * @throws \App\Exceptions\ExpectedException
     * @throws \League\Csv\CannotInsertRecord
     */
    private function handlePostRequest(Request $request, $page, $action = null)
    {
        switch ($page) {
            case (self::PAGE_GEO):
                $response = $this->geoSearch->processRequest($request);
                break;
            case (self::PAGE_ALI_ORDERS):
                $response = $this->aliOrders->processRequest($request);
                break;
            case (self::PAGE_XML_EMULATOR):
                $response = $this->xmlEmulator->processRequest($request);
                break;
            case (self::PAGE_SENDER):
                if ($action === self::ACTION_UPLOAD) {
                    $response = $this->postbackCollector->uploadArchiveFiles($request);
                }
                
                break;
        }
        
        if (isset($response) && $response instanceof Download) {
            return $response->get();
        }
        
        return $this->renderPage($page, ['response' => $response->getMessages()]);
    }
    
    /**
     * @param        $message
     * @param string $level
     *
     * @return Response
     */
    protected function getOnlyAlertResponse($message, $level = AlertMessageCollection::ALERT_TYPE_SUCCESS)
    {
        $response = new AlertMessageCollection();
        $response->addAlert($message, null, $level);
        
        return $this->renderPage('base', ['response' => $response->getMessages()]);
    }
    
    /**
     * @param string $page
     * @param array  $parameters
     *
     * @return Response
     */
    protected function renderPage(string $page, array $parameters = [])
    {
        $parameters['title'] = $this->getTitle($page);
        $parameters['description'] = $this->getDescription($page);
        
        return $this->render("tools/{$page}.html.twig", $parameters);
    }
    
    /**
     * @param string $xml
     *
     * @return Response
     */
    private function renderXmlPage(string $xml) : Response
    {
        return new Response($xml, 200, ['Content-Type' => 'text/xml']);
    }
    
    /**
     * @param $rout
     *
     * @return mixed
     */
    protected function getTitle($page)
    {
        if (array_key_exists($page, self::ROUTS_PARAMS)) {
            return self::ROUTS_PARAMS[$page]['title'];
        }
        
        return self::ROUTS_PARAMS[self::PAGE_MAIN]['title'];
    }
    
    /**
     * @param $page
     *
     * @return string
     */
    protected function getDescription($page)
    {
        return self::ROUTS_PARAMS[$page]['description'];
    }
}