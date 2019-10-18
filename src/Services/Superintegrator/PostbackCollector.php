<?php

namespace App\Services\Superintegrator;

use App\Entity\File;
use App\Entity\Message;
use App\Exceptions\ExpectedException;
use App\Services\File\CsvHandler;
use App\Services\TaskServiceInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Services\AbstractService;

/**
 * Парсит файлы архива и отправляет в бд для последующей отправки
 *
 * Class PostbackCollector
 *
 * @package App\Services\Superintegrator
 */
class PostbackCollector extends AbstractService implements TaskServiceInterface
{
    const DESTINATION = 'cityads';
    const FILE_NAME = 'archive';
    const FILE_TYPE = 'csv';
    const FILE_SYZE_LIMIT = 4 * 1000000;
    
    const URL_PIXEL_DOMAIN = 'http://cityadspix.com';
    const URL_POSTBACK_DOMAIN = 'http://cityads.ru';
    
    const URLS_LIMIT = 50;
    
    /**
     * @return mixed|void
     */
    public function start()
    {
        $this->collect();
    }
    
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
    
    /**
     * Ищет файлы архива и агрегирует данные для вставки в таблицу message
     */
    private function collect()
    {
        $urls = $this->getUrls();
        
        foreach ($urls as $url) {
            $message = new Message(self::DESTINATION, $url);
            $this->entityManager->persist($message);
        }
        
        $this->entityManager->flush();
    }
    
    /**
     * @return array|null
     */
    private function getUrls()
    {
        $urls       = [];
        $repository = $this->entityManager->getRepository(File::class);
        $files      = $repository->findBy(['type' => 'csv']);
        
        if (empty($files)) {
            return null;
        }
        
        foreach ($files as $file) {
            if (strpos($file->getFileName(), self::FILE_NAME) !== false) {
                $urls = array_merge($urls, $this->transform(stream_get_contents($file->getFileContent())));
                $this->entityManager->remove($file);
            }
        }
        
        return $urls;
    }
    
    /**
     * @param $content
     *
     * @return |null
     */
    private function transform($content)
    {
        if (!$content) {
            return null;
        }
        $content = explode("\n", $content);
        $headers = reset($content);
        $headers = array_flip(explode(';', $headers));
        array_shift($content);
        
        $requestTypeIndex = $headers['request_type'];
        $requestUrlIndex  = $headers['request_url'];
        
        foreach ($content as $row) {
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
        
        return !empty($urls) ? $urls : null;
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
    
    private function getNotSendedRequestCount()
    {
        $repository = $this->entityManager->getRepository(Message::class);
        $requests   = $repository->findBy(['sended' => 0, 'destination' => 'cityads']);
        
        return !empty($requests) ? count($requests) : 0;
    }
}