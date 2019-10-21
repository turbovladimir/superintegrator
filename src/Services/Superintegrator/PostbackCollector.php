<?php

namespace App\Services\Superintegrator;

use App\Orm\Entity\File;
use App\Orm\Entity\Message;
use App\Orm\Model\Message as MessageModel;
use App\Services\TaskServiceInterface;
use App\Services\AbstractService;
use Doctrine\ORM\EntityManagerInterface;

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
     * @var MessageModel
     */
    private $messageModel;
    
    /**
     * PostbackCollector constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param MessageModel           $messageModel
     */
    public function __construct(EntityManagerInterface $entityManager, MessageModel $messageModel)
    {
        $this->messageModel = $messageModel;
        parent::__construct($entityManager);
    }
    
    /**
     * @return mixed|void
     */
    public function start()
    {
        $this->collect();
    }
    
    /**
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getAwaitingPostbacks()
    {
        return $this->messageModel->getAwaitingMessagesCount(self::DESTINATION);
    }
    
    /**
     * Ищет файлы архива и агрегирует данные для вставки в таблицу message
     */
    private function collect()
    {
        $urls = $this->getUrls();
        $this->messageModel->saveMessages(self::DESTINATION, $urls);
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
}