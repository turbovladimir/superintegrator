<?php
//Стартуем сессии
session_start();
header('Content-Type: text/html; charset=utf-8');
//Подключаемся к базе данных.
include '../../autoload.php';
include_once '../../config.php';

$db = new simpleQuery($connectParams);
$table = 'geo_table';
$table = $db->selectAllFromTable($table, 100000000);

function build_table($table){
    // start table
    $html = '<span class="block"><table class="add_new_geo">';
    // header row
    $html .= '<tr>';
    foreach($table[0] as $key=>$value){
        $html .= '<th>' . htmlspecialchars($key) . '</th>';
    }
    $html .= '</tr>';

    // data rows
    foreach( $table as $key=>$value){
        $html .= '<tr>';
        foreach($value as $key2=>$value2){
            $html .= '<td>' . htmlspecialchars($value2) . '</td>';
        }
        $html .= '</tr>';
    }

    // finish table and return it

    $html .= '</table></span>';
    return $html;
}
echo '
<html>
<head>
    <title>Таблица гео</title>
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
    <script src="script.js"></script>
    <link rel="stylesheet" href="../../content/styles/geo.css">
    <link rel="shortcut icon" href="../../content/images/favicon.ico" type="image/x-icon">
</head>
<body>
<div class="data">
<span class="block">
    <div id="form">
    <p>Попробуй найти регион в таблице и добавь новое название для него</p>
       <input type="text" size="40"  name="city" id="city" placeholder="Новое название города">
        <input type="text" size="40"  name="id" id="id" placeholder="Id из списка">
        <input type="submit" onclick="$.addNewCity()" value="Отправить" name="send">
    <p><a href="../index.php">Назад</a></p>
    </div>
</span>';

echo build_table($table) .'
</body>
</html>
';
?>