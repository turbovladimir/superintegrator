<?php
if (isset($_POST['refresh'])) {
    include '../autoload.php';
    // $connectParams берется из файла конфига
    try{
        $db = new simpleQuery($connectParams);
        $count = $db->CountRowsOfTable($tablePostbacks);
        echo $count;
    }catch (dataBaseException $dbEx){
        $dbEx->getMessage();
    }
}
?>