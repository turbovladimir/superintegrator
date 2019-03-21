<?php
if (isset($_POST['refresh'])) {
    include '../autoload.php';
    include_once '../config.php';
    // $connectParams берется из файла конфига
    try{
        //connect
        $db = new simpleQuery($connectParams);

        // чистим таблицу от отправленных постбэков
        $db->rowQuery('DELETE FROM '.$tablePostbacks.' WHERE sended = 1');

        // посчитали остаток реквестов
        $count = $db->CountRowsOfTable($tablePostbacks);

        echo $count;
    }catch (dataBaseException $dbEx){
        $dbEx->getMessage();
    }

}
?>