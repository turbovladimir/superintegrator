<?php
header('Content-Type: text/html; charset=utf-8');
if (isset($_POST['city'])) { $city = $_POST['city']; if ($city == '') { unset($city);} }
if (isset($_POST['id'])) { $id = $_POST['id']; if ($id == '') { unset($id);} }

$city = trim($city);
$id = trim($id);
$table = 'geo_table';
//Подключаемся к базе данных.
include_once '../../db_connect/db.php';
//проверка на уже заведенное гео
$check_id = $db->getAll("SELECT * FROM ?n WHERE id= ?i", $table, $id);

if (!empty($check_id)) {

    $sql_insert_table ="INSERT INTO  ?n (`city`, `id`) VALUES (?s, ?i)";
    $db->query($sql_insert_table, $table, $city, $id);
    echo "Добавили новое название"."</br>";
} else {
    echo "ID не из списка"."</br>";
}


echo "<a href='index.php'>Назад</a>";


?>