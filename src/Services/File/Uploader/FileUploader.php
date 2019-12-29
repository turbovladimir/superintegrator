<?php
/**
 * Created by PhpStorm.
 * User: v.sadovnikov
 * Date: 18.10.2019
 * Time: 14:25
 */

namespace App\Services\File\Uploader;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Конвертирует файлы в данные бд
 *
 * Class FileUploader
 *
 * @package App\Services\File
 */
abstract class FileUploader
{
    /**
     * @var float|int
     */
    protected $filesizeLimit;
    
    /**
     * @var string
     */
    protected $fileType;
    
    /**
     * @var ServiceEntityRepository
     */
    protected $repository;
    
    public function __construct(ServiceEntityRepository $repository)
    {
        $this->repository = $repository;
    }
    
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
     * @throws UploadException
     */
    protected function checkFile(UploadedFile $file)
    {
        if ($file->getClientSize() > $this->filesizeLimit) {
            throw new UploadException('To large file');
        }
        
        if ($file->getClientOriginalExtension() !== $this->fileType) {
            throw new UploadException('Incorrect format of file, use only .' . $this->fileType);
        }
    }
    
    abstract public function upload(UploadedFile $file);
}