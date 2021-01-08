<?php

namespace App\Services\Tools;

use App\Repository\CsvFileRepository;
use App\Repository\MessageRepository;
use App\Response\ResponseMessage;
use App\Services\File\Uploader\ArchiveFileUploader;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Парсит файлы архива и отправляет в бд для последующей отправки
 * @todo отрефачить до более абстрактного или удалить
 * Class PostbackCollector
 *
 * @package App\Services\Tools
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
     * @var LoggerInterface
     */
    private $logger;
    
    /**
     * PostbackCollector constructor.
     *
     * @param CsvFileRepository $fileRepo
     * @param MessageRepository $messageRepo
     * @param ArchiveFileUploader      $uploader
     */
    public function __construct(
        CsvFileRepository $fileRepo,
        MessageRepository $messageRepo,
        ArchiveFileUploader $uploader,
        LoggerInterface $logger
    ) {
        $this->messageRepo = $messageRepo;
        $this->fileRepo = $fileRepo;
        $this->uploader = $uploader;
        $this->logger = $logger;
    }

    public function getToolInfo(): array {
        return [
            'title' => 'Sender',
            'description' => 'Переотправка постбэков и пикселей по файлам архива админки процессинга'
        ];
    }
    
    /**
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getAwaitingPostbacks()
    {
        $messageCount = $this->messageRepo->getAwaitingMessagesCount(self::DESTINATION);
        
        $response = new ResponseMessage('Awaiting postbacks', $messageCount);
        
        return $response->get();
    }
    
    /**
     * @param Request $request
     *
     * @return ResponseMessage
     * @throws \Exception
     */
    public function uploadArchiveFiles(Request $request)
    {
        $responseAlert = new ResponseMessage();
        $files = $request->files->all() ? : null;
    
        if (!$files) {
            return $responseAlert->addError('Cant find files for save');
        }
    
        $files = reset($files);
    
        foreach ($files as $file) {
            $this->uploader->upload($file);
            $responseAlert->addInfo("File {$file->getClientOriginalName()} have been successfully uploaded");
        }
        
        return $responseAlert;
    }
    
    /**
     * @return array
     * @throws \Doctrine\ORM\ORMException
     */
    public function getUrls()
    {
        $files = $this->fileRepo->getByEstimatedFileName(ArchiveFileUploader::FILE_NAME);
    
        if (!empty($files)) {
            $file = reset($files);
            $this->fileRepo->delete($file);
            $urls = $this->getUrlRequests(stream_get_contents($file->getFileContent()));
            $this->logger->info('Getting '. count($urls) . " urls from `{$file->getFileName()}`");
            
            return $urls;
        }
        
        return null;
    }
    
    /**
     * @param string $fileContent
     *
     * @return array
     */
    private function getUrlRequests(string $fileContent)
    {
        $urls = [];
        
        $rows = explode("\r\n", $fileContent);
        $headers     = reset($rows);
        $headers     = array_flip(explode(';', $headers));
        array_shift($rows);
        $rows = array_filter($rows);
        
        if (isset($headers['request_type']) && isset($headers['request_url'])) {
            $requestTypeIndex = $headers['request_type'];
            $requestUrlIndex  = $headers['request_url'];
    
            foreach ($rows as $row) {
                if (!empty($row)) {
                    $row = explode(';', $row);
                    
                    if (count($row) !== count($headers)) {
                        throw new \LogicException('Incorrect archive parsing');
                    }
            
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