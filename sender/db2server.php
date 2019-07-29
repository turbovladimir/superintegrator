<?php
date_default_timezone_set('Europe/Moscow');
$fileName = 'log/sending_to_city['.date("Y-m-d").'].log';
include '../autoload.php';

const AMOUNT_OF_ORDERS_PER_CYCLE = 50;
$sendedPostbacks = [];

$fd  = fopen($fileName, 'ab+');
$str = "start sending: ".date("Y-m-d H:i:s").'\n';
fwrite($fd, $str);
fclose($fd);
// подключаемся к бд и забираем данные

$db   = new simpleQuery($connectParams);
$urls = $db->selectColumnFromTable($tablePostbacks, 'url', AMOUNT_OF_ORDERS_PER_CYCLE);

if (empty($urls)) {
    exit;
}
$client = new \GuzzleHttp\Client();


foreach ($urls as $url) {
    $response = $client->request('GET', $url);
    
    if ($response->getStatusCode() === 200) {
        $sendedPostbacks[] = $url;
    }
}

// проверяем коннект
if (!($db->checkConnect())) {
    $db = new simpleQuery($connectParams);
}

if (!empty($sendedPostbacks)) {
    
    foreach ($sendedPostbacks as $postback) {
        $db->updateCellInTable($tablePostbacks, 'url', $postback, 'sended', '1'); // обновили
    }
}

?>