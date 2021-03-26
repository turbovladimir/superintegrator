<?php
/**
 * Created by PhpStorm.
 * User: v.sadovnikov
 * Date: 21.08.2019
 * Time: 14:42
 */

namespace App\Services\Tools;

use App\Response\FileDownloadResponse;
use App\Services\File\CsvFileManager;
use League\Csv\AbstractCsv;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Tools\Assistant\StringAssistant;

class AliOrdersService implements Tool
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

    public function getToolInfo(): array {
        return [
            'title' => 'Ali orders',
            'description' => 'Позволяет забирать подробную информацию о заказах алиэкспресс'
        ];
    }
    
    /**
     * @inheritDoc
     */
    public function process(array $parameters, $action = null) {
        if (isset($parameters['csv_export']) && !empty($parameters['orders'])) {
            $csv = $this->generateCsvReportFile(StringAssistant::splitId($parameters['orders']));

            return new FileDownloadResponse($csv->getPathname(), $this->getFileName());
        }
        
        throw new BadRequestHttpException('Empty orders field');
    }
    
    
    /**
     * @param $orders
     *
     * @return string
     * @throws \League\Csv\CannotInsertRecord
     */
    private function generateCsvReportFile(array $orders) : AbstractCsv {
    
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
     * @throws
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