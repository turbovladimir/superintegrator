<?php

namespace App\Services\Superintegrator;

use App\Repository\CsvFileRepository;
use App\Repository\MessageRepository;
use App\Response\AlertMessageCollection;
use App\Services\File\Uploader\ArchiveFileUploader;
use Symfony\Component\HttpFoundation\Request;

/**
 * Парсит файлы архива и отправляет в бд для последующей отправки
 *
 * Class PostbackCollector
 *
 * @package App\Services\Superintegrator
 */
class CityadsPostbackManager
{
    const DESTINATION = 'cityads';
    const URL_PIXEL_DOMAIN = 'http://cityadspix.com';
    const URL_POSTBACK_DOMAIN = 'http://cityads.ru';
    
    const URLS_LIMIT = 50;
    
    /**
     * @var MessageRepository
     */
    private $messageRepo;
    
    /**
     * @var CsvFileRepository
     */
    private $fileRepo;
    
    /**
     * @var ArchiveFileUploader
     */
    private $uploader;
    
    /**
     * PostbackCollector constructor.
     *
     * @param CsvFileRepository $fileRepo
     * @param MessageRepository $messageRepo
     * @param ArchiveFileUploader      $uploader
     */
    public function __construct(CsvFileRepository $fileRepo, MessageRepository $messageRepo, ArchiveFileUploader $uploader)
    {
        $this->messageRepo = $messageRepo;
        $this->fileRepo = $fileRepo;
        $this->uploader = $uploader;
    }
    
    /**
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getAwaitingPostbacks()
    {
        $messageCount = $this->messageRepo->getAwaitingMessagesCount(self::DESTINATION);
        
        $response = new AlertMessageCollection('Awaiting postbacks', $messageCount);
        
        return $response->getMessages();
    }
    
    /**
     * @param Request $request
     *
     * @return AlertMessageCollection
     * @throws \Exception
     */
    public function uploadArchiveFiles(Request $request)
    {
        $responseAlert = new AlertMessageCollection();
        $files = $request->files->all() ? : null;
    
        if (!$files) {
            return $responseAlert->addAlert('Cant find files for save', AlertMessageCollection::ALERT_TYPE_DANGER);
        }
    
        $files = reset($files);
    
        foreach ($files as $file) {
            $this->uploader->upload($file);
        }
        
        return $responseAlert->addAlert('Files have been successfully uploaded');
    }
    
    /**
     * @return array
     * @throws \Doctrine\ORM\ORMException
     */
    public function getUrls()
    {
        $urls = [];
        $files = $this->fileRepo->findBy(['type' => 'csv']);
    
        if (!empty($files)) {
            foreach ($files as $file) {
                if ($this->isArchiveFile($file)) {
                    $urls = array_merge($urls, $this->getUrlRequests(stream_get_contents($file->getFileContent())));
                    $this->fileRepo->getEntityManager()->remove($file);
                }
            }
        }
        
        return $urls;
    }
    
    /**
     * @param string $fileContent
     *
     * @return array
     */
    private function getUrlRequests(string $fileContent)
    {
        $urls = [];
        
        $fileContent = explode("\n", $fileContent);
        $headers     = reset($fileContent);
        $headers     = array_flip(explode(';', $headers));
        array_shift($fileContent);
        
        if (isset($headers['request_type']) && isset($headers['request_url'])) {
            $requestTypeIndex = $headers['request_type'];
            $requestUrlIndex  = $headers['request_url'];
    
            foreach ($fileContent as $row) {
                if (!empty($row)) {
                    $row = explode(';', $row);
            
                    if ($row[$requestTypeIndex] === 'pixel') {
                        $url = self::URL_PIXEL_DOMAIN.$row[$requestUrlIndex];
                    } elseif ($row[$requestTypeIndex] === 'postback') {
                        $url = self::URL_POSTBACK_DOMAIN.$row[$requestUrlIndex];
                    } else {
                        continue;
                    }
            
                    $urls[] = $this->paramEncode($url);
                }
            }
        }
        
        return $urls;
    }
    
    /**
     * @param $url
     *
     * @return string
     */
    private function paramEncode($url)
    {
        $urlArr       = explode('?', $url);
        $urlPath      = str_replace('"', '', trim($urlArr[0], '"'));
        $urlParams    = str_replace('""', '"', trim($urlArr[1], '"'));
        $encodeParams = urlencode($urlParams);
        
        return $urlPath.'?'.$encodeParams;
        
    }
}