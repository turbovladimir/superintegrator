<?php
/**
 * Created by PhpStorm.
 * User: v.sadovnikov
 * Date: 19.07.2019
 * Time: 18:03
 */
date_default_timezone_set('Europe/Moscow');
include '../autoload.php';
include_once '../config.php';
$fileName = 'log/clear_table['.date("Y-m-d").'].log';

$fd = fopen($fileName , 'ab+');
$str = "start sending: ". date("Y-m-d H:i:s") . '\n';
fwrite($fd, $str);
fclose($fd);

// $connectParams берется из файла конфига
try {
    $db = new simpleQuery($connectParams);
    $db->rowQuery('DELETE FROM '.$tablePostbacks.' WHERE sended = 1');
} catch (dataBaseException $dbEx) {
    $dbEx->getMessage();
}
?>