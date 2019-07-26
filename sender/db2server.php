<?php
date_default_timezone_set('Europe/Moscow');
$fileName = 'log/sending_to_city['.date("Y-m-d").'].log';
include '../autoload.php';
include_once '../config.php';

const AMOUNT_OF_ORDERS_PER_CYCLE = 500;
$sendedPostbacks = [];

$fd  = fopen($fileName, 'ab+');
$str = "start sending: ".date("Y-m-d H:i:s").'\n';
fwrite($fd, $str);
fclose($fd);
// подключаемся к бд и забираем данные

$db   = new simpleQuery($connectParams);
$urls = $db->selectColumnFromTable($tablePostbacks, 'url', AMOUNT_OF_ORDERS_PER_CYCLE);

// отправляем реквесты если они есть:
if (!empty($urls)) {
    $AC = new AngryCurl('my_callback');
    
    
    foreach ($urls as $url) {
        // adding URL to queue
        $AC->get($url, $headers = null, $options = null);
        
    }
    
    // setting amount of threads and starting connections
    $AC->execute(200);
    
    function my_callback ($responseStr, $info, RollingCurlRequest $request) {
        global $sendedPostbacks;
        if ($info['http_code'] === 200) {
            $sendedPostbacks[] = $request->url;
        }
    }
    
    unset($AC);
    
    
    // проставляем флаги успешной отправки в таблицу с постбэками
    $counter = count($sendedPostbacks);
    
    // проверяем коннект
    if (!($db->checkConnect())) {
        $db = new simpleQuery($connectParams);
    }
    
    for ($i = 0; $i < $counter; $i++) {
        $db->updateCellInTable($tablePostbacks, 'url', $sendedPostbacks[$i], 'sended', '1'); // обновили
    }
}

?>