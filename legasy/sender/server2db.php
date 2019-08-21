<?php
    /**
     * Created by PhpStorm.
     * User: v.sadovnikov
     * Date: 14.03.2019
     * Time: 13:43
     */
    
    include '../autoload.php';
    include_once '../config.php';
// $connectParams берется из файла конфига
    try {
        $files = $_FILES;
        
        $files2arr = new ExcelToPhp($files);
        
        if ($files2arr->ExcelChecker('archive', 'application/vnd.ms-excel')) {
            
            $array = $files2arr->toArray();
            
            $db = new simpleQuery($connectParams);
            
            //перед этим отчищаем таблицу `postbacktable`
            $db->clearTable($tablePostbacks);
            
            // заливаем:
            $count = count($array);
            
            for ($i = 0; $i < $count; $i++) {
                $db->insertToTable($tablePostbacks, 'url', $array[$i]);
            }
            
            
        } else {
            echo 'invalid files';
        }
    } catch (dataBaseException $dbEx) {
        echo $dbEx->getMessage();
    } catch (fileException $fEx) {
        echo $fEx->getMessage();
    }


?>