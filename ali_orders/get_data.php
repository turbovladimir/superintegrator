<?php
include_once '../Classes/func.php';
download_send_headers("ali_data_export_" . date("Y-m-d H:i:s") . ".csv");

function fetchOrdersFromApiAlibaba($orders) {
    $ordersStr = '';
    for ($i = 0; $i < count($orders); $i++){
        
        $ordersStr .= ','.$orders[$i];
    }
    $ordersStr = substr($ordersStr, 1);
    $url = "https://gw.api.alibaba.com/openapi/param2/2/portals.open/api.getOrderStatus/30056?appSignature=9FIO77dDIidM&orderNumbers=". $ordersStr;
    
    $result =  curl($url);
    $result = json_decode($result, TRUE);
    $orders = $result['result']['orders'];
    $sortOrders = [];
    foreach ($orders as $order) {
        $sortOrders[] = fixAlibabaDataFormat($order);
    }
    return $sortOrders;
}

function fixAlibabaDataFormat($order) {
    if (!isset($order['transactionTime'])) {
        $order['transactionTime'] = '';
    }
    ksort($order);
    return $order;
}

if (!empty($_POST['data'])){
        $arrayResponseFromApi = [];
        $arrayResult = [];
        $orders = $_POST['data'];
        $orders = explode(',', $orders);

        // разделяем 2 сценария , если свыше 100 ордеров и наоборот
        if (count($orders) > 100){

            // дробим по 100 ордеров и отправляем в алибабу
            $bigOrders = array_chunk($orders, 100);
            
            foreach ($bigOrders as $ordersChunk) {
                $arrayResponseFromApi[] = fetchOrdersFromApiAlibaba($ordersChunk);
            }
            
            $arrayResult = array_merge(...$arrayResponseFromApi); // отличное решение, элементы массива = подмассивы через оператор ... встраиваются в функцию мерж

        } else{
            $arrayResult = fetchOrdersFromApiAlibaba($orders);
        }
        
        // преобразуем массив в цсв
    echo array2csv($arrayResult);
    }
?>