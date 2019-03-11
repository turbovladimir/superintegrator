<?php
include_once 'functions.php';
include_once 'check_file.php';

if ($error_file === 0){
    ## в цикле забираем из файлс в переменную массив данные построчно
    $data = array();

    for ($i = 0; $i < $file_count; $i++) {
        $file = $_FILES[$i]['tmp_name'];
        $handle = fopen($file, "r");

        while (($row = fgetcsv($handle, 10000, "\n")) !== FALSE) {
            $get_file_arr[$i][] = implode(";", $row);
        }

        array_shift($get_file_arr[$i]);
        $data = array_merge($data, $get_file_arr[$i]); // delete header and merge in data
    }

    ## $data наша переменная с массивом

    $postb = []; // запишем в нее наши потсбэки и пиксели, писаться будет всё с фильтром по наличию определенных значнеий в строке
    for ($i = 0; $i < count($data); $i++) {
        $line = $data[$i];
        $line_arr = explode(";", $line);
        if ($line_arr[5] != '' && $line_arr[16] != ''){
            @$type  = strval($line_arr[5]);
            @$req   = strval($line_arr[16]);
        }
        $req    = trim($req, '"');
        $req    =  str_replace('""','"', $req);
        if ($type !== "" && $type !== "request_url" && $type == "postback") {
            $request = "http://cityads.ru";
            $str = $request . $req;
            array_push($postb, $str);
        }
        if ($type !== "" && $type !== "request_url" && $type == "pixel") {
            $request = "http://cityadspix.com";
            $str = $request . $req;
            array_push($postb, $str);
        }

    }

    include_once '../db_connect/db.php'; // insert safemysql
    ## отправляем в БД
    $table = "postbacktable";

    $reset_table = "TRUNCATE TABLE ?n";
    $db->query($reset_table, $table);

    $sql_insert_table = "INSERT INTO ?n (`id`, `url`) VALUES (NULL, ?s);";
    for ($z =0; $z < count($postb); $z++){

        $db->query($sql_insert_table, $table, $postb[$z]);

    }
## пишем логи
    $url_amount = $z;
    $sql_update = "UPDATE `table_log` SET `url_amount`= ?i where `id`= 1";
    $db->query($sql_update, $url_amount);
} else {
    echo "error";
}

?>