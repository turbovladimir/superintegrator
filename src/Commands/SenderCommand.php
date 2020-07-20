<?php
/**
 * Created by PhpStorm.
 * User: v.sadovnikov
 * Date: 04.09.2019
 * Time: 11:10
 */

namespace App\Commands;

use App\Exceptions\EmptyDataException;
use App\Services\Sender\MessageManager;
use App\Services\Superintegrator\CityadsPostbackManager;
use Psr\Log\LoggerInterface;

/**
 * Class CronCommand
 *
 * @package App\Commands
 */
class SenderCommand extends BaseDaemon
{
    protected static $defaultName = 'postback:sender';
    private $messageManager;
    private $cityadsPostbackManager;
    
    /**
     * SenderCommand constructor.
     *
     * @param LoggerInterface        $logger
     * @param CityadsPostbackManager $cityadsPostbackManager
     * @param MessageManager         $messageManager
     */
    public function __construct(
        LoggerInterface $logger,
        CityadsPostbackManager $cityadsPostbackManager,
        MessageManager $messageManager)
    {
        $this->messageManager         = $messageManager;
        $this->cityadsPostbackManager = $cityadsPostbackManager;
        parent::__construct($logger);
    }
    
    /**
     * @throws \App\Exceptions\EmptyDataException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    protected function process() : bool
    {
        $this->parseFilesToArchiveUrls();
    
        try {
            $this->messageManager->send();
        } catch (EmptyDataException $exception) {
            $this->logger->info($exception->getMessage());
            
            return false;
        }
        
        return true;
    }
    
    /**
     * @throws
     */
    private function parseFilesToArchiveUrls() : void
    {
        $this->logger->info('Start parse archive files');
        $createdMessages = 0;
        
        while ($urls = $this->cityadsPostbackManager->getUrls()) {
            $createdMessages += $urlCnt = count($urls);
    
            foreach ($urls as $url) {
                $this->messageManager->saveMessage(CityadsPostbackManager::DESTINATION, $url);
            }
        }
        
        $this->logger->info("`{$createdMessages}` messages were created");
    }
}