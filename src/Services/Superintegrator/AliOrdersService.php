<?php
/**
 * Created by PhpStorm.
 * User: v.sadovnikov
 * Date: 21.08.2019
 * Time: 14:42
 */

namespace App\Services\Superintegrator;

use Symfony\Component\HttpClient\HttpClient;
use App\Services\AbstractService;

//todo реализовать нормально файловый сервис
class AliOrdersService extends AbstractService
{
    protected $isFileService = true;
    const URL = 'https://gw.api.alibaba.com/openapi/param2/2/portals.open/api.getOrderStatus/30056?appSignature=9FIO77dDIidM&orderNumbers=';
    const LIMIT_OF_ORDERS_PER_REQUEST = 100;
    
    /**
     * @var
     */
    public $fileName;
    
    /**
     * @param $parameters
     *
     * @return string
     */
    public function process($parameters)
    {
        $orders = $parameters['orders'];
        
        if (count($orders) > self::LIMIT_OF_ORDERS_PER_REQUEST) {
            
            // дробим по 100 ордеров и отправляем в алибабу
            $bigOrders = array_chunk($orders, self::LIMIT_OF_ORDERS_PER_REQUEST);
            
            foreach ($bigOrders as $ordersChunk) {
                $arrayResponseFromApi[] = self::fetchOrders($ordersChunk);
            }
            
            $advertiserOrders = array_merge(
                ...$arrayResponseFromApi
            ); // отличное решение, элементы массива = подмассивы через оператор ... встраиваются в функцию мерж
            
        } else {
            $advertiserOrders = $this->fetchOrders($orders);
        }
        
        $date           = date('y-m-d h:i:s');
        $this->fileName = "Aliexpress_orders_{$date}.csv";
        
        foreach ($advertiserOrders as $order) {
            if (!isset($fileContent)) {
                $fileContent = implode(',', array_keys($order))."\n";
            }
            $fileContent .= implode(',', $order)."\n";
        }
        
        return $fileContent;
    }
    
    private function fetchOrders($orders)
    {
        $httpClient = HttpClient::create();
        $response   = $httpClient->request('GET', self::URL.implode(',', $orders));
        $content    = $response->toArray();
        $orders     = $content['result']['orders'];
        foreach ($orders as $order) {
            $sortOrders[] = $this->fixTransactionTime($order);
        }
        return $sortOrders;
    }
    
    private function fixTransactionTime($order)
    {
        if (!isset($order['transactionTime'])) {
            $order['transactionTime'] = '';
        }
        ksort($order);
        return $order;
    }
}