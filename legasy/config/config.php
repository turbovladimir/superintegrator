<?php
    // подключение к бд
    $host   =   'sk8kilay.beget.tech';
    //$host   =   'localhost';
    $user =   'sk8kilay_test';
    $pass =   '123456';
    $database =   'sk8kilay_test';
    
    //имена таблиц
    $tableLog = 'table_log_test'; // тестовые таблицы постфикс _test, боевые без постфикса
    $tablePostbacks = 'postbacktable'; // тестовые таблицы постфикс _test, боевые без постфикса
    
    define('HOST', $host);
    define('DATABASE', $database);
    define('USER', $user);
    define('PASSWORD', $pass);
    define('LOG_TABLE', $tableLog);

?>

