<?php
    include_once '../Classes/SPEED_EXEC_TEST.php';
    include_once '../config.php';
    include_once '../Classes/POSTMAN.php';

    $table  =   'postbacktable';
    $LogTableName = 'table_log';
    $files = $_FILES;

    $my_test = new SPEED_EXEC_TEST('SendToDb.php','log_file');
    $my_test->start_test();

    $little_postman = new POSTMAN($host, $dbname, $dbuser, $dbpass, $table, $LogTableName);



    $little_postman->SendtoDb('archive', $files);

    $my_test->end_test();
?>