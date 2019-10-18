<?php
/**
 * Created by PhpStorm.
 * User: v.sadovnikov
 * Date: 18.10.2019
 * Time: 17:52
 */

namespace App\Services\File;

use League\Csv\Stream;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Exceptions\ExpectedException;
use League\Csv\Reader;

class CsvHandler extends FileHandler
{
    const FILE_TYPE_CSV = 'csv';
    
    /**
     * @param UploadedFile $file
     *
     * @throws ExpectedException
     * @throws \League\Csv\Exception
     */
    public function uploadCSV(UploadedFile $file)
    {
        $this->checkFile($file);
        
        $fileName = $file->getClientOriginalName();
        $this->upload($fileName, self::FILE_TYPE_CSV, fopen($file->getRealPath(), 'rb'));
    }
    
    /**
     * @param UploadedFile $file
     *
     * @throws ExpectedException
     */
    protected function checkFile(UploadedFile $file)
    {
        if ($file->getClientSize() > self::FILE_SYZE_LIMIT ) {
            throw new ExpectedException('To large file');
        }
    
        if ($file->getClientOriginalExtension() !== self::FILE_TYPE_CSV) {
            throw new ExpectedException('Incorrect format of file, use only .' . self::FILE_TYPE_CSV);
        }
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