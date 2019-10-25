<?php
/**
 * Created by PhpStorm.
 * User: v.sadovnikov
 * Date: 18.10.2019
 * Time: 14:25
 */

namespace App\Services\File;


use App\Orm\Entity\File;
use App\Services\AbstractService;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;

/**
 * Конвертирует файлы в данные бд
 *
 * Class FileUploader
 *
 * @package App\Services\File
 */
abstract class FileHandler extends AbstractService
{
    
    const FILE_SYZE_LIMIT = 4 * 1000000;
    
    /**
     * @var float|int
     */
    private $filesizeLimit = self::FILE_SYZE_LIMIT;
    
    abstract public function uploadFileAction(Request $request);
    
    abstract protected function checkFile(UploadedFile $file);
    
    /**
     * @param $limit
     */
    public function setFileSyzeLimit($limit)
    {
        $this->filesizeLimit = $limit;
    }
    
    /**
     * @param $name
     * @param $type
     * @param $content
     *
     * @throws \Exception
     */
    protected function upload($name, $type, $content)
    {
        $fileEntity = new File();
        $fileEntity->setFileName($name);
        $fileEntity->setType($type);
        $fileEntity->setFileContent($content);
        $this->entityManager->persist($fileEntity);
        $this->entityManager->flush();
    }
}