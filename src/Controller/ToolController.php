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
use App\Services\Superintegrator\CityadsPostbackManager;
use App\Services\Superintegrator\XmlEmulatorService;
use Psr\Log\LoggerInterface;

class ToolController extends BaseController
{
    public const PAGE_MAIN = 'base';
    public const PAGE_GEO = 'geo';
    public const PAGE_ALI_ORDERS = 'ali_orders';
    public const XML_EMULATOR = 'xml_emulator';
    public const SENDER = 'sender';
    
    public const ACTION_GET_XML_PAGE = 'xml';
    public const ACTION_GET_JSON_PAGE = 'json';
    public const ACTION_NEW = 'new';
    public const ACTION_UPLOAD = 'upload';
    
    private const ROUTS_PARAMS = [
        self::PAGE_MAIN           => [
            'title' => 'Main page',
            'description' => 'Добро пожаловать в наш скромный ламповый сервис'
        ],
        self::PAGE_GEO            => [
            'title' => 'Geo searching',
            'description' => 'Инструмент для поиска id гео объектов (страны, регионы, города), для получения id можно использовать как 2х буквенные коды стран так и полные ENG наименования'
        ],
        self::PAGE_ALI_ORDERS     => [
            'title' => 'Ali orders',
            'description' => 'Позволяет забирать подробную информацию о заказах алиэкспресс'
        ],
        self::XML_EMULATOR        => [
            'title' => 'Xml emulator',
            'description' => 'При помощи данного инструмента вы можете создавать ссылки эмулирующие работу API с форматом ответа xml'
        ],
        self::SENDER              => [
            'title' => 'Sender',
            'description' => 'Переотправка постбэков и пикселей по файлам архива админки процессинга'
        ],
        self::ACTION_GET_XML_PAGE => ['title' => ''],
    ];
    
    private $geoSearch;
    private $xmlEmulator;
    private $postbackManager;
    private $aliOrders;
    private $logger;
    
    /**
     * HttpController constructor.
     *
     * @param LoggerInterface        $logger
     * @param GeoSearchService       $geoSearch
     * @param XmlEmulatorService     $xmlEmulator
     * @param CityadsPostbackManager $postbackManager
     * @param AliOrdersService       $aliOrders
     */
    public function __construct(
        LoggerInterface $logger,
        GeoSearchService $geoSearch,
        XmlEmulatorService $xmlEmulator,
        CityadsPostbackManager $postbackManager,
        AliOrdersService $aliOrders
    ) {
        $this->logger          = $logger;
        $this->geoSearch       = $geoSearch;
        $this->xmlEmulator     = $xmlEmulator;
        $this->postbackManager = $postbackManager;
        $this->aliOrders       = $aliOrders;
    }
    
    /**
     * @param         $tool
     * @param         $action
     * @param Request $request
     *
     * @return Response
     */
    public function index($tool, $action = null, Request $request)
    {
        if (!array_key_exists($tool, self::ROUTS_PARAMS)) {
            return $this->mainPage();
        }
        
        try {
            if ($request->getMethod() === 'POST') {
                return $this->handlePostRequest($request, $tool, $action);
            }
            
            return $this->handleGetRequets($request, $tool, $action);
        } catch (\Exception $exception) {
            $response = new AlertMessageCollection('Обнаружена ошибка', $exception->getMessage(), AlertMessageCollection::ALERT_TYPE_DANGER);
            
            return $this->mainPage(['response' => $response->getMessages()]);
        }
    }
    
    /**
     * @param Request $request
     * @param         $tool
     * @param null    $action
     *
     * @return Response
     * @throws \App\Exceptions\ExpectedException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    private function handleGetRequets(Request $request, $tool, $action = null)
    {
        $options = [];
        
        switch ($tool) {
            case (self::SENDER):
                $options['response'] = $this->postbackManager->getAwaitingPostbacks();
                break;
            case (self::XML_EMULATOR):
                if ($action === self::ACTION_GET_XML_PAGE) {
                        return $this->renderXmlPage($this->xmlEmulator->getXml($request));
                }
                
                if ($action !== self::ACTION_NEW) {
                    $options = array_merge($options, $this->xmlEmulator->getTableWithXmlTemplates());
                }
        }
        
        return $this->renderPage($tool, $action, $options);
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
            case (self::XML_EMULATOR):
                $response = $this->xmlEmulator->processRequest($request);
                break;
            case (self::SENDER):
                if ($action === self::ACTION_UPLOAD) {
                    $response = $this->postbackManager->uploadArchiveFiles($request);
                    $action = null;
                }
                
                break;
        }
        
        if (isset($response) && $response instanceof Download) {
            return $response->get();
        }
        
        return $this->renderPage($page, $action, ['response' => $response->getMessages()]);
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
        
        return $this->mainPage(['response' => $response->getMessages()]);
    }
    
    /**
     * @param string $page
     * @param null   $action
     * @param array  $parameters
     *
     * @return Response
     */
    protected function renderPage(string $tool, $action = null, array $parameters = [])
    {
        $pathToTemplate = $action !== null ? "{$tool}/{$action}" : "{$tool}/index";
        $parameters = array_merge($parameters, $this->getContent($tool));
        
        return $this->render("tools/{$pathToTemplate}.html.twig", $parameters);
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
     * @param $tool
     *
     * @return array
     */
    protected function getContent($tool)
    {
        if (array_key_exists($tool, self::ROUTS_PARAMS)) {
            return [
                'title' => self::ROUTS_PARAMS[$tool]['title'],
                'description' => self::ROUTS_PARAMS[$tool]['description'],
            ];
        }
        
        return [
            'title' => self::ROUTS_PARAMS[self::PAGE_MAIN]['title'],
            'description' => self::ROUTS_PARAMS[self::PAGE_MAIN]['description']
        ];
    }
}