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
use \GuzzleHttp\Client;

class SenderService extends AbstractService
{
    const FILE_NAME_CONTAINS = 'archive';
    const FILE_TYPE = 'csv';
    const FILE_SYZE_LIMIT = 4 * 1000000;
    
    const URL_PIXEL_DOMAIN = 'http://cityadspix.com';
    const URL_POSTBACK_DOMAIN = 'http://cityads.ru';
    
    const URLS_LIMIT = 50;
    
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
        $client = new Client();
        $repository = $this->entityManager->getRepository(ArchiveRows::class);
        $urls = $repository->findBy(['sended' => 0], [], self::URLS_LIMIT);
        
        if (!empty($urls)) {
            
            foreach ($urls as $urlEntity) {
                $url = $urlEntity->getRow();
                
                try {
                    $response = $client->request('GET', $url);
    
                    if ($response->getStatusCode() === 200) {
                        $urlEntity->setSended();
                    }
                } catch (\Exception $e) {
                    sleep(1);
                    continue;
                }
            }
            $this->entityManager->flush();
        }
        
        return true;
    }
    
    public function clearDb()
    {
        $sendedUrls = $this->entityManager->getRepository(ArchiveRows::class)->findBy(['sended' => 1], [], 20);
        
        if (!empty($sendedUrls)) {
            foreach ($sendedUrls as $urlEntity) {
                $this->entityManager->remove($urlEntity);
            }
            $this->entityManager->flush();
        }
    }
    
    
    private function pushToDb($file)
    {
        $filePath = $file->getRealPath();
        $urls = $this->getUrls($filePath);
        
        if ($urls === null) {
            throw new ExpectedException('There is no urls in file');
        }
    
        foreach ($urls as $url) {
            $entityPostback = new ArchiveRows();
            $entityPostback->setRow($url);
            $this->entityManager->persist($entityPostback);
            $this->entityManager->flush();
        }
    }
    
    private function getUrls($filePath)
    {
        $handler = fopen($filePath, 'rb');
        while(($row = fgetcsv($handler, 10000, "\n")) !== FALSE){
            $content[] = $row;
        }
    
        fclose($handler);
        $headers = reset($content);
        $headers = array_flip(explode(';', $headers[0])) ;
        array_shift($content);
        
        $requestTypeIndex = $headers['request_type'];
        $requestUrlIndex = $headers['request_url'];
    
        foreach ($content as $row) {
            if (empty($row)) {
                continue;
            }
            $row = explode(';', reset($row));
        
            if ($row[$requestTypeIndex] === 'pixel') {
                $url = self::URL_PIXEL_DOMAIN.$row[$requestUrlIndex];
            } elseif ($row[$requestTypeIndex] === 'postback') {
                $url = self::URL_POSTBACK_DOMAIN.$row[$requestUrlIndex];
            } else {
                continue;
            }
            
            $urls[] = $this->paramEncode($url);
        }
        
        return !empty($urls) ? $urls : null;
    }
    
    private function paramEncode($url)
    {
        $urlArr = explode('?', $url);
        $urlPath = str_replace('"', '', trim($urlArr[0], '"'));
        $urlParams = str_replace('""', '"', trim($urlArr[1], '"'));
        $encodeParams = urlencode($urlParams);
        
        return $urlPath.'?'.$encodeParams;
        
    }
    
    
    private function validateFile($file)
    {
        if (stripos($file->getClientOriginalName(), self::FILE_NAME_CONTAINS) || $file->getClientOriginalExtension() !== self::FILE_TYPE) {
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