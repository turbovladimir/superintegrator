<?php
include '../autoload.php';

const AMOUNT_OF_ORDERS_PER_CYCLE = 50;
$sendedPostbacks = [];

// подключаемся к бд и забираем данные
$db   = new simpleQuery($connectParams);

try {
    $db->rawQuery('DELETE FROM '.$tablePostbacks.' WHERE sended = 1');
} catch (\Exception $e) {
    $e->getMessage();
}

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