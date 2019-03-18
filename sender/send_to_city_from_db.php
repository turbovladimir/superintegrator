<?php
include '../autoload.php';
include_once '../config.php';


    // пишем в логи в папку скорость выполнения скрипта
    $my_test = new SPEED_EXEC_TEST('local test db to city','log_file');
    $my_test->start_test();

    // подключаемся к бд и забираем данные
try {
    $db = new simpleQuery($connectParams);
    $urls = $db->selectColumnFromTable($tablePostbacks, 'url', 3000);
    $db->deleteRowsFromTable($tablePostbacks, 3000);

    // отправляем мультикурлом
    $multCurl = new MultiCurl($urls);
    $multCurl->Start();

    //апдейтим таблицуу с логами
    $count = $db->CountRowsOfTable($tablePostbacks); // посчитали остаток реквестов
    $db->updateCellInTable($tableLog, 'url_amount', $count, 1); // обновили
    }catch (dataBaseException $ex) {
        //Выводим сообщение об исключении.
        echo $ex->getMessage();
    }
    $my_test->end_test();
    # Рабочий крон
    # %progdir%\modules\wget\bin\wget.exe -q --no-cache http://test/city_dooDOS/send_to_city_from_db.php
    # public_html/sender/send_to_city_from_db.php
    # wget -O /dev/null https://superintegrator.tk/sender/send_to_city_from_db.php
    #запись типа «1 * * * *» будет означать запуск задачи каждую первую минуту часа, т.е. она будет выполняться каждый час;
    #запись «*/2 * * * *» будет запускать задачу через каждые две минуты;
    #запись «2-4 * * * *» будет соответствовать запуску задачи 3 раза в течении каждого часа во 2,3 и 4 минуту;
    #запись «* * 1 * *» будет соответствовать ежемесячному запуску задачи первого числа месяца.
?>