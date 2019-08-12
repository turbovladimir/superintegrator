<?php
/**
 * Created by PhpStorm.
 * User: v.sadovnikov
 * Date: 12.08.2019
 * Time: 19:30
 */

class MigrationApplyer
{
    
    public static function start($fileName)
    {
            $simpleQ = new simpleQuery();
            $fullPath = MIGRATION_FOLDER . $fileName;
            $handler = fopen($fullPath, 'rb');
            $contents = fread($handler, filesize($fullPath));
            $contents = str_replace("\n", '', $contents);
            $queries = explode(';', $contents);
            
            foreach ($queries as $query) {
                try {
                    $simpleQ->rawQuery($query);
                } catch (\Exception $dbException) {
                    $errors[] =  $dbException->getMessage();
                }
                
            }
            
            fclose($handler);
            
            if (!empty($errors)) {
                print_r($errors);
            }
    }
}