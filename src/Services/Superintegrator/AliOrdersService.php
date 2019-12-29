<?php
/**
 * Created by PhpStorm.
 * User: v.sadovnikov
 * Date: 21.08.2019
 * Time: 14:42
 */

namespace App\Services\Superintegrator;

use App\Exceptions\ExpectedException;
use App\Response\Download;
use App\Services\File\CsvFileManager;
use App\Utils\StringHelper;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Request;

class AliOrdersService
{
    const FILE_NAME = 'aliexpress_orders';
    const LIMIT_OF_ORDERS_PER_REQUEST = 100;
    const HEADERS = [
        'baseCommissionRate',
        'category',
        'commission',
        'commissionRate',
        'country',
        'estimatedCommission',
        'extraParams',
        'finalPaymentAmount',
        'isHotProduct',
        'orderNumber',
        'orderStatus',
        'orderTime',
        'paymentAmount',
        'pid',
        'product',
        'publisherGmvRate',
        'trackingId',
        'transactionTime',
    ];
    
    /**
     * @var string
     */
    private $apiUrl;
    
    /**
     * @param string $aliexpressApiUrl
     */
    public function __construct(string $aliexpressApiUrl)
    {
        $this->apiUrl = $aliexpressApiUrl;
    }
    
    /**
     * @param Request $request
     *
     * @return Download
     * @throws ExpectedException
     * @throws \League\Csv\CannotInsertRecord
     */
    public function processRequest(Request $request)
    {
        parse_str($request->getContent(), $parameters);
        
        if (isset($parameters['csv_export']) && !empty($parameters['orders'])) {
            $name = $this->getFileName();
            $content = $this->generateFileContent(StringHelper::splitId($parameters['orders']));

            return new Download($name, $content);
        }
        
        throw new ExpectedException('Empty orders field');
    }
    
    
    /**
     * @param $orders
     *
     * @return string
     * @throws \League\Csv\CannotInsertRecord
     */
    private function generateFileContent(array $orders)
    {
    
        if (count($orders) > self::LIMIT_OF_ORDERS_PER_REQUEST) {
        
            // дробим по 100 ордеров и отправляем в алибабу
            $bigOrders = array_chunk($orders, self::LIMIT_OF_ORDERS_PER_REQUEST);
        
            foreach ($bigOrders as $ordersChunk) {
                $arrayResponseFromApi[] = $this->fetchOrders($ordersChunk);
            }
        
            $advertiserOrders = array_merge(
                ...$arrayResponseFromApi
            ); // элементы массива = подмассивы через оператор ... встраиваются в функцию мерж
        
        } else {
            $advertiserOrders = $this->fetchOrders($orders);
        }
    
        return CsvFileManager::generateFile($advertiserOrders);
    }
    
    
    private function getFileName()
    {
        $date           = date('y-m-d h:i:s');
        
        return self::FILE_NAME . "_{$date}.csv";
    }
    
    /**
     * @param $orders
     *
     * @return array
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    private function fetchOrders($orders)
    {
        $sortOrders = [];
        $httpClient = HttpClient::create();
        $response   = $httpClient->request('GET', $this->apiUrl.implode(',', $orders));
        $content    = $response->toArray();
        
        if (!isset($content['result']['orders'])) {
            return [];
        }
        
        $orders     = $content['result']['orders'];
        
        foreach ($orders as $order) {
            $sortOrders[] = $this->filterOrder($order);
        }
        return $sortOrders;
    }
    
    /**
     * @param $order
     *
     * @return mixed
     */
    private function filterOrder($order)
    {
        foreach (self::HEADERS as $key) {
            if (!isset($order[$key])) {
                $order[$key] = '';
            }
        }
        
        ksort($order);
        
        return $order;
    }
}