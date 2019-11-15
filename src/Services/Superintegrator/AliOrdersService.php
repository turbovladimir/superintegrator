<?php
/**
 * Created by PhpStorm.
 * User: v.sadovnikov
 * Date: 21.08.2019
 * Time: 14:42
 */

namespace App\Services\Superintegrator;

use App\Exceptions\ExpectedException;
use App\Services\File\CsvHandler;
use Symfony\Component\HttpClient\HttpClient;

class AliOrdersService
{
    
    const URL = 'https://gw.api.alibaba.com/openapi/param2/2/portals.open/api.getOrderStatus/30056?appSignature=9FIO77dDIidM&orderNumbers=';
    const LIMIT_OF_ORDERS_PER_REQUEST = 100;
    
    /**
     * @param $orders
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws ExpectedException
     */
    public function process($orders)
    {
        if (!$orders) {
            throw new ExpectedException('Empty orders field');
        }
        
        $name = $this->getFileName();
        $content = $this->generateFileContent(json_decode($orders));
        
        return CsvHandler::download($name, $content);
    }
    
    
    /**
     * @param $orders
     *
     * @return string
     */
    private function generateFileContent($orders)
    {
    
        if (count($orders) > self::LIMIT_OF_ORDERS_PER_REQUEST) {
        
            // дробим по 100 ордеров и отправляем в алибабу
            $bigOrders = array_chunk($orders, self::LIMIT_OF_ORDERS_PER_REQUEST);
        
            foreach ($bigOrders as $ordersChunk) {
                $arrayResponseFromApi[] = $this->fetchOrders($ordersChunk);
            }
        
            $advertiserOrders = array_merge(
                ...$arrayResponseFromApi
            ); // отличное решение, элементы массива = подмассивы через оператор ... встраиваются в функцию мерж
        
        } else {
            $advertiserOrders = $this->fetchOrders($orders);
        }
    
        $header = array_keys(reset($advertiserOrders));
        foreach ($advertiserOrders as $order) {
            $records[] = array_values($order);
        }
    
        return CsvHandler::generateFile($header, $records);
    }
    
    
    private function getFileName()
    {
        $date           = date('y-m-d h:i:s');
        
        return "Aliexpress_orders_{$date}.csv";
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