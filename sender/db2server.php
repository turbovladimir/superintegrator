<?php
include '../autoload.php';
include_once '../config.php';

const SLEEP_TIME_SECONDS = 30;
$cycles = 20;

while (true) {

    if ($cycles === 0) {
        break;
    }
    
// подключаемся к бд и забираем данные
    try {
        $db   = new simpleQuery($connectParams);
        $urls = $db->selectColumnFromTable($tablePostbacks, 'url', 1000);
        
        function CheckOkResponse($responseStr, $info, RollingCurlRequest $request)
        {
            global $responseOK;
            if ($info['http_code'] === 200) {
                $responseOK[] = $request->url;
            }
        }
        
        // отправляем реквесты если они есть:
        if (!empty($urls)) {
            $AC = new AngryCurl('CheckOkResponse');
            
            foreach ($urls as $url) {
                // adding URL to queue
                $AC->get($url, $headers = null, $options = null);
                
            }
            
            // setting amount of threads and starting connections
            $AC->execute(200);
            
            unset($AC);
            
            
            // проставляем флаги успешной отправки в таблицу с постбэками
            $counter = count($responseOK);
            
            // проверяем коннект
            if (!($db->checkConnect())) {
                $db = new simpleQuery($connectParams);
            }
            
            for ($i = 0; $i < $counter; $i++) {
                $db->updateCellInTable($tablePostbacks, 'url', $responseOK[$i], 'sended', '1'); // обновили
            }
        }
    } catch (dataBaseException $ex) {
        //Выводим сообщение об исключении.
        echo $ex->getMessage();
    }
    
    sleep(SLEEP_TIME_SECONDS);
    $cycles --;
}
# Рабочий крон
# %progdir%\modules\wget\bin\wget.exe -q --no-cache http://test/city_dooDOS/send_to_city_from_db.php
# public_html/sender/send_to_city_from_db.php
# wget -O /dev/null https://superintegrator.tk/sender/send_to_city_from_db.php
#запись типа «1 * * * *» будет означать запуск задачи каждую первую минуту часа, т.е. она будет выполняться каждый час;
#запись «*/2 * * * *» будет запускать задачу через каждые две минуты;
#запись «2-4 * * * *» будет соответствовать запуску задачи 3 раза в течении каждого часа во 2,3 и 4 минуту;
#запись «* * 1 * *» будет соответствовать ежемесячному запуску задачи первого числа месяца.
?>