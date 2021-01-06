<?php
/**
 * Created by PhpStorm.
 * User: v.sadovnikov
 * Date: 02.09.2019
 * Time: 13:45
 */

namespace App\Controller;

use App\Response\AlertMessage;
use App\Response\Download;
use App\Services\Tools\Cryptor;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use App\Services\Tools\AliOrdersService;
use App\Services\Tools\GeoSearchService;
use App\Services\Tools\CityadsPostbackManager;
use App\Services\Tools\XmlEmulatorService;
use Psr\Log\LoggerInterface;
use Twig\Error\LoaderError;

class ToolController extends AbstractController
{
    public const PAGE_MAIN = 'base';
    public const PAGE_GEO = 'geo';
    public const PAGE_ALI_ORDERS = 'ali_orders';
    public const XML_EMULATOR = 'xml_emulator';
    public const CRYPTOR = 'cryptor';
    public const SENDER = 'sender';
    
    public const ACTION_GET_XML_PAGE = 'xml';
    public const ACTION_GET_JSON_PAGE = 'json';
    public const ACTION_NEW = 'new';
    public const ACTION_UPLOAD = 'upload';
    public const ACTION_DELETE = 'delete';
    public const ACTION_SAVE = 'save';
    
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
        self::CRYPTOR              => [
            'title' => 'Cryptor',
            'description' => 'Encrypt/decrypt string by salt'
        ],
        self::SENDER              => [
            'title' => 'Sender',
            'description' => 'Переотправка постбэков и пикселей по файлам архива админки процессинга'
        ],
        self::ACTION_GET_XML_PAGE => ['title' => ''],
    ];
    
    private $env;
    private $geoSearch;
    private $xmlEmulator;
    private $postbackManager;
    private $aliOrders;
    private $logger;
    private $cryptor;
    
    /**
     * HttpController constructor.
     *
     * @param string                 $env
     * @param LoggerInterface        $logger
     * @param GeoSearchService       $geoSearch
     * @param XmlEmulatorService     $xmlEmulator
     * @param CityadsPostbackManager $postbackManager
     * @param AliOrdersService       $aliOrders
     * @param Cryptor                $cryptor
     */
    public function __construct(
        LoggerInterface $logger,
        GeoSearchService $geoSearch,
        XmlEmulatorService $xmlEmulator,
        CityadsPostbackManager $postbackManager,
        AliOrdersService $aliOrders,
        Cryptor $cryptor,
        string $env
    ) {
        $this->env             = $env;
        $this->logger          = $logger;
        $this->geoSearch       = $geoSearch;
        $this->xmlEmulator     = $xmlEmulator;
        $this->postbackManager = $postbackManager;
        $this->aliOrders       = $aliOrders;
        $this->cryptor         = $cryptor;
    }
    
    /**
     * @param         $tool
     * @param         $action
     * @param Request $request
     *
     * @return Response
     */
    public function index($tool = null, $action = null, Request $request)
    {
        if (!array_key_exists($tool, self::ROUTS_PARAMS)) {
            return $this->redirect('/');
        }
        
        try {
            if ($request->getMethod() === 'POST') {
                return $this->handlePostRequest($request, $tool, $action);
            }
            
            return $this->handleGetRequets($request, $tool, $action);
        } catch (\Exception $exception) {
            $response = new AlertMessage('Обнаружена ошибка', $exception->getMessage(), AlertMessage::TYPE_DANGER);
            
            if ($this->env === 'dev') {
                $response->addAlert('Trace', $exception->getTraceAsString(), AlertMessage::TYPE_DANGER);
            }
            
            return $this->mainPageAction(['response' => $response->get()]);
        }
    }
    
    /**
     * @param array $parameters
     *
     * @return Response
     */
    public function mainPageAction($parameters = []) : Response {
        return $this->render('base.html.twig', $parameters);
    }
    
    /**
     * @param Request $request
     * @param         $tool
     * @param null    $action
     *
     * @return Response
    * @throws
     */
    private function handleGetRequets(Request $request, $tool, $action = null)
    {
        $options = [];
        
        switch ($tool) {
            case (self::SENDER):
                $options['response'] = $this->postbackManager->getAwaitingPostbacks();
                break;
            case (self::XML_EMULATOR):
                switch ($action) {
                    case self::ACTION_DELETE:
                        $options['response'] = $this->xmlEmulator->delete($request->get('id'));
                        break;
                    case self::ACTION_NEW:
                        break;
                    case self::ACTION_GET_XML_PAGE:
                        return $this->renderXmlPage($this->xmlEmulator->getXml($request));
                    default:
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
     * @return array|Response
     * @throws
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
                if ($action === self::ACTION_SAVE) {
                    $response = $this->xmlEmulator->create($request);
                }
                break;
            case (self::SENDER):
                if ($action === self::ACTION_UPLOAD) {
                    $response = $this->postbackManager->uploadArchiveFiles($request);
                }
                break;
            case (self::CRYPTOR):
                $response = $this->cryptor->processRequest($request);
        }
        
        if (isset($response) && $response instanceof Download) {
            return $response->get();
        }
        
        return $this->renderPage($page, null, ['response' => $response->get()]);
    }
    
    /**
     * @param        $message
     * @param string $level
     *
     * @return Response
     */
    protected function getOnlyAlertResponse($message, $level = AlertMessage::TYPE_SUCCESS)
    {
        $response = new AlertMessage();
        $response->addAlert($message, null, $level);
        
        return $this->mainPageAction(['response' => $response->get()]);
    }
    
    /**
     * @param string $tool
     * @param null   $action
     * @param array  $parameters
     *
     * @return Response
     */
    protected function renderPage(string $tool, $action = null, array $parameters = [])
    {
        $pathToTemplate = $action !== null ? "{$tool}/{$action}" : "{$tool}/index";
        $parameters = array_merge($parameters, $this->getContent($tool));
        
        try {
            return $this->render("tools/{$pathToTemplate}.html.twig", $parameters);
        } catch (LoaderError $e) {
            return $this->mainPageAction();
        }
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