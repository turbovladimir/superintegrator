<?php
/**
 * Created by PhpStorm.
 * User: v.sadovnikov
 * Date: 18.10.2019
 * Time: 17:52
 */

namespace App\Services\File;

use League\Csv\AbstractCsv;
use League\Csv\Reader;
use League\Csv\Writer;

class CsvFileManager
{
    /**
     * @param array $content
     *
     * @return string
     * @throws \League\Csv\CannotInsertRecord
     */
    public static function generateFile(array $content) : AbstractCsv {
        if (empty($content)) {
            return '';
        }
        
        $csv = Writer::createFromString('');
        $csv->insertOne(array_keys(reset($content)));
        $csv->insertAll($content);
        
        return $csv;
    }
    
    /**
     * Отдает содержимое цсв в виде массива
     *
     * @param $filePath
     * @param $delimiter
     *
     * @return array
     * @throws \League\Csv\Exception
     */
    public static function readCsvFromPath($filePath, $delimiter = ';')
    {
        $csv = Reader::createFromPath($filePath, 'rb');
        
        return self::read($csv, $delimiter);
    }
    
    
    /**
     * @param        $stream
     * @param string $delimiter
     *
     * @return array
     */
    public static function readCsvFromSource($stream, $delimiter = ';')
    {
        $csv = Reader::createFromStream($stream);
        
        return self::read($csv, $delimiter);
    }
    
    private static function read(Reader $reader, $delimiter)
    {
        $reader->setHeaderOffset(0);
        $reader->setDelimiter($delimiter);
        
        return $reader->jsonSerialize();
    }
}