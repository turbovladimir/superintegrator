<?php
/**
 * Created by PhpStorm.
 * User: v.sadovnikov
 * Date: 18.10.2019
 * Time: 14:25
 */

namespace App\Services\File;


use App\Exceptions\ExpectedException;
use App\Orm\Entity\File;
use App\Services\AbstractService;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Конвертирует файлы в данные бд
 *
 * Class FileUploader
 *
 * @package App\Services\File
 */
class FileUploader extends AbstractService
{
    const DEFAULT_FILE_TYPE_CSV = 'csv';
    const DEFAULT_FILE_SYZE_LIMIT = 4 * 1000000;
    
    /**
     * @var float|int
     */
    private $filesizeLimit = self::DEFAULT_FILE_SYZE_LIMIT;
    
    /**
     * @var string
     */
    private $fileType = self::DEFAULT_FILE_TYPE_CSV;
    
    /**
     * @param $limit
     */
    public function setFileSyzeLimit($limit)
    {
        $this->filesizeLimit = $limit;
    }
    
    /**
     * @param $type
     */
    public function setFileType($type)
    {
        $this->fileType = $type;
    }
    
    /**
     * @param UploadedFile $file
     *
     * @throws ExpectedException
     */
    public function checkFile(UploadedFile $file)
    {
        if ($file->getClientSize() > self::DEFAULT_FILE_SYZE_LIMIT ) {
            throw new ExpectedException('To large file');
        }
        
        if ($file->getClientOriginalExtension() !== self::DEFAULT_FILE_TYPE_CSV) {
            throw new ExpectedException('Incorrect format of file, use only .' . self::DEFAULT_FILE_TYPE_CSV);
        }
    }
    
    /**
     * @param $name
     * @param $type
     * @param $content
     *
     * @throws \Exception
     */
    public function upload(UploadedFile $file)
    {
        $this->checkFile($file);
        $fileEntity = new File();
        $fileEntity->setFileName($file->getClientOriginalName());
        $fileEntity->setType($file->getClientOriginalExtension());
        $fileEntity->setFileContent(fopen($file->getRealPath(), 'rb'));
        $this->entityManager->persist($fileEntity);
        $this->entityManager->flush();
    }
}