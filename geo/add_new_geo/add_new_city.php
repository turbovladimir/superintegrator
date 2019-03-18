<?php
if (isset($_POST['city'], $_POST['id'])){
    include '../../autoload.php';
    include_once '../../config.php';

    $city = $_POST['city'];
    $id = $_POST['id'];
//подключаемся к бд и тянем таблицу
    $db = new simpleQuery($connectParams);
    $table = 'geo_table';
    $array = $db->selectAllFromTable($table, 100000000);

    $counter = count($array);
    $find = 0;

    for ($i= $counter; $i !== -1; $i--){
        if ($array[$i]['id'] === $id){
            if ($array[$i]['city'] !== $city){
                echo 'Добавили новое название';
                $values = sprintf('(\'%s\', \'%s\')', $id, $city);
                $query = 'INSERT INTO '.$table.'(`id`, `city`) VALUES '.$values.';';
                $db->rowQuery($query);
                $find = 1;
                break;
            }else if ($array[$i]['city'] === $city){
                echo 'Такое название уже есть в списке';
                $find = 1;
                break;
            }
        }
    }

    if ($find === 0){
        echo 'ID не из списка';
    }
}

?>