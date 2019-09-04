<?php
/**
 * Created by PhpStorm.
 * User: v.sadovnikov
 * Date: 21.08.2019
 * Time: 19:24
 */

namespace App\Services;

use App\Entity\ArchiveRows;
use App\Exceptions\ExpectedException;
use Symfony\Component\HttpFoundation\Request;

class SenderService extends AbstractService
{
    const FILE_NAME_CONTAINS = 'archive';
    const FILE_TYPE = 'csv';
    const FILE_SYZE_LIMIT = 4 * 1000000;
    
    /**
     * @param $parameters
     *
     * @return false|string
     * @throws \Exception
     */
    public function process($parameters)
    {
        if (array_key_exists('ask_server_about_requests', $parameters)) {
            return $this->getNotSendedRequestCount();
        }
    }
    
    public function sendDataFromFiles2Server(Request $request)
    {
        $files = $request->files->all() ?: false;
    
        if ($files) {
            $files = reset($files);
        
            foreach ($files as $file) {
                $this->validateFile($file);
                $this->pushToDb($file);
            }
        }
    
        return 'Files have been successfully added in queue';
    }
    
    public function sendFromDb()
    {
        $repository = $this->entityManager->getRepository(ArchiveRows::class);
        $firstRowEntity = $repository->findOneBy(['sended' => 0]);
        $rows = $firstRowEntity->getRows();
        return $rows;
    }
    
    private function pushToDb($file)
    {
        $filePath = $file->getRealPath();
        $hendler = fopen($filePath, 'rb');
        $contents = fread($hendler, filesize($filePath));
        fclose($hendler);
        $rows = explode("\n", $contents);
        array_shift($rows);
        $rowsParts = array_chunk($rows, 100);
        
        foreach ($rowsParts as $part) {
            
            if (!empty($part) && !empty(reset($part))) {
                $entityPostback = new ArchiveRows();
                $entityPostback->setRows(json_encode($part, JSON_UNESCAPED_SLASHES ));
                $this->entityManager->persist($entityPostback);
                $this->entityManager->flush();
            }
        }
    }
    
    private function validateFile($file)
    {
        if ($file->getClientOriginalName() !== self::FILE_NAME_CONTAINS || $file->getClientOriginalExtension() !== self::FILE_TYPE) {
            throw new ExpectedException('Invalid file type');
        }
        
        if ($file->getClientSize() > self::FILE_SYZE_LIMIT) {
            throw new ExpectedException('To large file');
        }
    }
    
    private function getNotSendedRequestCount()
    {
        $repository = $this->entityManager->getRepository(ArchiveRows::class);
        $requests = $repository->findBy(['sended' => 0]);
    
        return !empty($requests) ? count($requests) : 0;
    }
}