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
use App\Services\File\FileUploader;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use App\Services\Superintegrator\AliOrdersService;
use App\Services\Superintegrator\GeoSearchService;
use App\Services\Superintegrator\PostbackCollector;
use App\Services\Superintegrator\XmlEmulatorService;
use Psr\Log\LoggerInterface;

class HttpController extends BaseController
{
    private const PAGE_MAIN = 'base';
    private const PAGE_GEO = 'geo';
    private const PAGE_ALI_ORDERS = 'ali_orders';
    private const PAGE_XML_EMULATOR = 'xml_emulator';
    private const PAGE_XML_TYPE = 'xml';
    private const PAGE_SENDER = 'sender';
    
    private const ROUTS_PARAMS = [
        self::PAGE_MAIN         => ['title' => 'Main page'],
        self::PAGE_GEO          => ['title' => 'Geo searching'],
        self::PAGE_ALI_ORDERS   => ['title' => 'Ali orders'],
        self::PAGE_XML_EMULATOR => ['title' => 'Xml emulator'],
        self::PAGE_SENDER       => ['title' => 'Sender'],
        self::PAGE_XML_TYPE       => ['title' => ''],
    ];
    
    private $geoSearch;
    private $xmlEmulator;
    private $postbackCollector;
    private $aliOrders;
    private $uploader;
    private $logger;
    
    /**
     * HttpController constructor.
     *
     * @param LoggerInterface    $logger
     * @param GeoSearchService   $geoSearch
     * @param XmlEmulatorService $xmlEmulator
     * @param PostbackCollector  $postbackCollector
     * @param AliOrdersService   $aliOrders
     * @param FileUploader       $uploader
     */
    public function __construct(
        LoggerInterface $logger,
        GeoSearchService $geoSearch,
        XmlEmulatorService $xmlEmulator,
        PostbackCollector $postbackCollector,
        AliOrdersService $aliOrders,
        FileUploader $uploader
    ) {
        $this->logger            = $logger;
        $this->geoSearch         = $geoSearch;
        $this->xmlEmulator       = $xmlEmulator;
        $this->postbackCollector = $postbackCollector;
        $this->aliOrders         = $aliOrders;
        $this->uploader          = $uploader;
    }
    
    /**
     * @param         $page
     * @param         $action
     * @param Request $request
     *
     * @return Response
     */
    public function index($page, $action = null, Request $request)
    {
        try {
            if ($request->getMethod() === 'POST') {
                return $this->handlePostRequest($request, $page, $action);
            }
            
            return $this->handleGetRequets($request, $page, $action);
        } catch (\Exception $exception) {
            $response = new AlertMessageCollection('Обнаружена ошибка', $exception->getMessage(), AlertMessageCollection::ALERT_TYPE_DANGER);
            
            return $this->renderPage($page, ['response' => $response->getMessages()]);
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
        
        if (!array_key_exists($page, self::ROUTS_PARAMS)) {
            $page = 'base';
        }
        
        if ($action === 'new') {
            $options['new_form'] = 1;
        }
        
        switch ($page) {
            case (self::PAGE_SENDER):
                $options['response'] = $this->postbackCollector->getAwaitingPostbacks();
                break;
            case (self::PAGE_XML_EMULATOR):
                $options = array_merge($options, $this->xmlEmulator->getTableWithXmlTemplates());
                break;
            case (self::PAGE_XML_TYPE):
                return $this->renderXmlPage($request);
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
        if ($action === 'upload') {
            return $this->upload($request);
        }
        
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
        
        if (isset($response) && $response instanceof Download) {
            return $response->get();
        }
        
        return $this->renderPage($page, ['response' => $response->getMessages()]);
    }
    
    /**
     * @param Request $request
     *
     * @return Response
     * @throws \Exception
     */
    private function upload(Request $request)
    {
        $files = $request->files->all() ? : null;
        
        if (!$files) {
            return $this->getOnlyAlertResponse('Cant find files for save', AlertMessageCollection::ALERT_TYPE_DANGER);
        }
        
        $files = reset($files);
        
        foreach ($files as $file) {
            $this->uploader->upload($file);
        }
        
        return $this->getOnlyAlertResponse('Files have been successfully uploaded');
    }
    
    /**
     * @param string $page
     * @param array  $parameters
     *
     * @return Response
     */
    private function renderPage(string $page, array $parameters = [])
    {
        $parameters['title'] = $this->getTitle($page);
        $parameters['description'] = $this->setDescription($page);
        
        return $this->render($page . '.html.twig', $parameters);
    }
    
    /**
     * @param Request $request
     *
     * @return Response
     */
    private function renderXmlPage(Request $request) : Response
    {
        parse_str($request->getQueryString(), $parameters);
        
        if (empty($parameters['key'])) {
            return $this->getOnlyAlertResponse('Cannot parse key from url', AlertMessageCollection::ALERT_TYPE_DANGER);
        }
        
        $xml = $this->xmlEmulator->getXmlByKey($parameters['key']);
        
        if ($xml === null) {
            return $this->getOnlyAlertResponse('Incorrect or expired key', AlertMessageCollection::ALERT_TYPE_DANGER);
        }
        
        
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
}