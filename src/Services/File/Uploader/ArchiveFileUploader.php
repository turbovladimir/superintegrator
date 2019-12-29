<?php
/**
 * Created by PhpStorm.
 * User: v.sadovnikov
 * Date: 25.12.2019
 * Time: 18:51
 */

namespace App\Services\File\Uploader;


use App\Entity\CsvFile;
use App\Repository\CsvFileRepository;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ArchiveFileUploader extends FileUploader
{
    public const FILE_NAME = 'archive';
    protected $fileType = 'csv';
    protected $filesizeLimit = 4 * 1000000; // 4 Mb
    
    /**
     * ArchiveFileUploader constructor.
     *
     * @param CsvFileRepository $repository
     */
    public function __construct(CsvFileRepository $repository)
    {
        parent::__construct($repository);
    }
    
    /**
     * @param UploadedFile $file
     *
     * @throws UploadException
     * @throws \App\Exceptions\ExpectedException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function upload(UploadedFile $file)
    {
        $this->checkFile($file);
        $fileEntity = new CsvFile();
        $fileEntity->setFileName($file->getClientOriginalName());
        $fileEntity->setType($file->getClientOriginalExtension());
        $fileEntity->setFileContent(fopen($file->getRealPath(), 'rb'));
        $em = $this->repository->getEntityManager();
        $em->persist($fileEntity);
        $em->flush();
    }
    
    /**
     * @param UploadedFile $file
     *
     * @return bool
     */
    private function isArchiveFile(UploadedFile $file)
    {
        return strpos($file->getClientOriginalName(), self::FILE_NAME) !== false;
    }
    
    /**
     * @param UploadedFile $file
     *
     * @throws UploadException
     * @throws \App\Exceptions\ExpectedException
     */
    protected function checkFile(UploadedFile $file)
    {
        if (!$this->isArchiveFile($file)) {
            throw new UploadException('There is not archive file');
        }
        
        parent::checkFile($file);
    }
}