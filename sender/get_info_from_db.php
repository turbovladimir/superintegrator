<?php
if (isset($_POST['refresh'])){
    include_once '../db_connect/db.php';
    $get_db_log = $db->getAll("SELECT * FROM `table_log`");
    if(!empty($get_db_log)) {
        foreach ($get_db_log[0] as $value => $key) echo $value . '= ' . $key . "</br>";
    }
    
}

?>