<?php
header('Content-Type: text/html; charset=utf-8');
if (isset($_POST['city'])) { $city = $_POST['city']; if ($city == '') { unset($city);} }
if (isset($_POST['id'])) { $id = $_POST['id']; if ($id == '') { unset($id);} }

include '../../autoload.php';
include_once '../../config.php';

//подключаемся к бд и тянем таблицу
$db = new simpleQuery($connectParams);
$table = 'geo_table';
$array = $db->selectAllFromTable($table, 100000000);

$city = trim($city);
$id = trim($id);
$counter = count($array);
$find = 0;

for ($i= 0; $i < $counter; $i++){
    if ($array[$i]['id'] === $id){
        if ($array[$i]['city'] !== $city){
            echo 'Добавили новое название'.'</br>';
            $db->insertToTable($table, 'city', $city);
            $find = 1;
        }else if ($array[$i]['city'] === $city){
            echo 'Такое название уже есть в списке'.'</br>';
            $find = 1;
        }
    }
}

if ($find === 0){
    echo 'ID не из списка'.'</br>';
}

echo "<a href='index.php'>Назад</a>";


?>