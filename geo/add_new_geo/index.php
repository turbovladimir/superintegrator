<?php
//Стартуем сессии
session_start();
header('Content-Type: text/html; charset=utf-8');
//Подключаемся к базе данных.

include_once '../../db_connect/db.php';
include_once '../get_data.php';

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
    <link rel="stylesheet" href="../../content/styles/geo.css">
    <link rel="shortcut icon" href="../../content/images/favicon.ico" type="image/x-icon">
</head>
<body>
<div class="data">
<span class="block">
    <div>
    <p>Попробуй найти регион в таблице и добавь новое название для него</p>
    <form action="add_new_city.php" method="post">
       <input type="text" size="40"  name="city" placeholder="Новое название города">
        <input type="text" size="40"  name="id" placeholder="Id из списка">
        <input type="submit" value="Отправить">
    </form>
    <p><a href="../index.php">Назад</a></p>
    </div>
</span>';

echo build_table($table) .'
</body>
</html>
';
?>