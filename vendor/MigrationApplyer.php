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
        try {
            $query = new simpleQuery();
            $fullPath = MIGRATION_FOLDER . $fileName;
            $handler = fopen($fullPath, 'rb');
            $contents = fread($handler, filesize($fullPath));
            $query->rawQuery($contents);
            fclose($handler);
        } catch (\Exception $exception) {
            echo $exception->getMessage();
        }
    }
}