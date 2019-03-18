<?php
if (isset($_POST['refresh'])) {
    include '../autoload.php';
    include_once '../config.php';
    try{

        $log = new simpleQuery($connectParams);

        $urlAmount = $log->selectColumnFromTable($tableLog, 'url_amount', 1);

        echo $urlAmount[0];
    }catch (dataBaseException $dbEx){
        $dbEx->getMessage();
    }

}
?>