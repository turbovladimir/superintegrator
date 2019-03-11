<?php
// вытаскиваем таблицу add_new_geo в массив table
$table = $db->getAll("SELECT city, id FROM `geo_table`");
?>