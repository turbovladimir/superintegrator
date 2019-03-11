<?php
    include_once '../Classes/SPEED_EXEC_TEST.php';
    include_once '../db_connect/connect_vars.php';
    include_once '../Classes/POSTMAN.php';

    $my_test = new SPEED_EXEC_TEST('local test db to city','log_file');
    $my_test->start_test();

    $little_postman = new POSTMAN($host, $dbname, $dbuser, $dbpass, $table);


    $LogReport = $little_postman->SendFromDb(3000,1);

    $my_test->SetReport($LogReport);
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