<?php
/**
 * Created by PhpStorm.
 * User: v.sadovnikov
 * Date: 18.10.2019
 * Time: 14:25
 */

namespace App\Services\File;


use App\Entity\File;
use App\Exceptions\ExpectedException;
use App\Services\AbstractService;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use League\Csv\Reader;

/**
 * Конвертирует файлы в данные бд
 *
 * Class FileUploader
 *
 * @package App\Services\File
 */
class FileUploader extends AbstractService
{
    const FILE_TYPE_CSV = 'csv';
    const FILE_SYZE_LIMIT = 4 * 1000000;
    
    /**
     * @var float|int
     */
    private $filesizeLimit = self::FILE_SYZE_LIMIT;
    
    
    /**
     * @param UploadedFile $file
     *
     * @throws ExpectedException
     * @throws \League\Csv\Exception
     */
    public function uploadCSV(UploadedFile $file)
    {
        if ($file->getClientSize() > self::FILE_SYZE_LIMIT ) {
            throw new ExpectedException('To large file');
        }
        
        if ($file->getClientOriginalExtension() !== self::FILE_TYPE_CSV) {
            throw new ExpectedException('Incorrect format of file, use only .' . self::FILE_TYPE_CSV);
        }
    
        $fileContent = $this->getFileContent($file->getRealPath());
        $fileName = $file->getClientOriginalName();
        $this->upload($fileName, json_encode($fileContent));
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
    public function getFileContent($filePath, $delimiter = ';')
    {
        $csv = Reader::createFromPath($filePath, 'rb');
        $csv->setHeaderOffset(0);
        $csv->setDelimiter($delimiter);
        return $csv->jsonSerialize();
    }
    
    /**
     * @param $limit
     */
    public function setFileSyzeLimit($limit)
    {
        $this->filesizeLimit = $limit;
    }
    
    /**
     * @todo с сохранениями различных типов могут возникнуть трудности из-за оъема данных
     *
     * @param $fileName
     * @param $fileContent
     */
    private function upload($fileName, $fileContent)
    {
        $fileEntity = new File();
        $fileEntity->setFileName($fileName);
        $fileEntity->setFileContent($fileContent);
        $fileEntity->setAddedAt();
        $this->entityManager->persist($fileEntity);
        $this->entityManager->flush();
    }
}