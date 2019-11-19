<?php
/**
 * Created by PhpStorm.
 * User: v.sadovnikov
 * Date: 02.09.2019
 * Time: 13:45
 */

namespace App\Controller;

use App\Orm\Entity\Superintegrator\TestXml;
use App\Response\AlertMessageCollection;
use App\Response\Download;
use App\Services\File\CsvUploader;
use App\Services\File\FileUploader;
use \App\Services\Superintegrator\XmlEmulatorService;
use Doctrine\ORM\EntityManager;
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
    private $logger;
    
    /**
     * @param string             $page
     * @param Request            $request
     * @param LoggerInterface    $logger
     * @param GeoSearchService   $geoSearch
     * @param XmlEmulatorService $xmlEmulator
     * @param PostbackCollector  $postbackCollector
     * @param AliOrdersService   $aliOrders
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
        AliOrdersService $aliOrders
    ) {
        $response                = [];
        $this->request           = $request;
        $this->logger            = $logger;
        $this->requestContent    = urldecode($this->request->getContent());
        $this->geoSearch         = $geoSearch;
        $this->xmlEmulator       = $xmlEmulator;
        $this->postbackCollector = $postbackCollector;
        $this->aliOrders         = $aliOrders;
    
        $page === '/' ? $page = 'base' : null;
        
        try {
            //todo перерабтать роутинг под 404
            if ($this->request->getMethod() === 'GET') {
                switch ($page) {
                    case (self::PAGE_SENDER):
                        return $this->render('sender.html.twig', ['notSendedPostbacks' => $postbackCollector->getAwaitingPostbacks()]);
                        break;
                    case (self::PAGE_XML_EMULATOR):
                        $collection = $xmlEmulator->getCollection();
                        
                        return $this->render('xml_emulator.html.twig', $collection ? ['table_head' => array_keys(reset($collection)), 'xml_collection' => $collection] : []);
                        break;
                    default:
                        return $this->render("{$page}.html.twig");
                }
            } else {
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
     * @param Request      $request
     * @param FileUploader $uploader
     *
     * @return Response
     * @throws \Exception
     */
    public function uploadFiles(Request $request, FileUploader $uploader)
    {
        $files = $request->files->all() ? : null;
    
        if (!$files) {
            return $this->getOnlyAlertResponse('Cant find files for save', AlertMessageCollection::ALERT_TYPE_DANGER);
        }
    
        $files = reset($files);
    
        foreach ($files as $file) {
            $uploader->upload($file);
        }
    
        return $this->getOnlyAlertResponse('Files have been successfully uploaded');
    }
    
    /**
     * @param Request            $request
     * @param XmlEmulatorService $xmlEmulator
     *
     * @return Response
     */
    public function getXmlPage(Request $request, XmlEmulatorService $xmlEmulator)
    {
        parse_str($request->getQueryString(), $parameters);
        
        if (empty($parameters['key'])) {
            return $this->getOnlyAlertResponse('Cannot parse key from url', AlertMessageCollection::ALERT_TYPE_DANGER);
        }
    
        $xml = $xmlEmulator->getXmlByKey($parameters['key']);
        
        if ($xml === null) {
            return $this->getOnlyAlertResponse('Incorrect or expired key', AlertMessageCollection::ALERT_TYPE_DANGER);
        }
        
        
        return new Response($xml, 200, ['Content-Type' => 'text/xml']);
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
        
        return $this->render("base.html.twig", ['response' => $response->getMessages()]);
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