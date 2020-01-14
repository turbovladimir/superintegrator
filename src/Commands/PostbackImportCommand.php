<?php
/**
 * Created by PhpStorm.
 * User: v.sadovnikov
 * Date: 04.09.2019
 * Time: 11:10
 */

namespace App\Commands;


use App\Repository\MessageRepository;
use App\Services\Superintegrator\CityadsPostbackManager;
use Psr\Log\LoggerInterface;


/**
 * Class PostbackImportCommand
 *
 * @package App\Commands
 */
class PostbackImportCommand extends BaseDaemon
{
    protected static $defaultName = 'collect_postbacks';
    protected $description = 'Вытасивает из архивных файлов и ставит в очередь постбэки';
    
    private $postbackManager;
    private $messageRepository;
    
    /**
     * PostbackImportCommand constructor.
     *
     * @param LoggerInterface        $logger
     * @param CityadsPostbackManager $postbackManager
     * @param MessageRepository      $messageRepository
     */
    public function __construct(LoggerInterface $logger, CityadsPostbackManager $postbackManager, MessageRepository $messageRepository)
    {
        $this->postbackManager   = $postbackManager;
        $this->messageRepository = $messageRepository;
        parent::__construct($logger);
    }
    
    /**
     * @throws \Throwable
     */
    protected function process() : void
    {
        $this->messageRepository->getEntityManager()->transactional(function () {
            $urls = $this->postbackManager->getUrls();
            $urlsCount = count($urls);
            
            if ($urlsCount === 0) {
                return;
            }
            
            $this->logger->info('Getting '. $urlsCount . ' urls from files');
            $this->messageRepository->saveMessages(CityadsPostbackManager::DESTINATION, $urls);
            $this->output->writeln('Postbacks successfully was be imported in messages');
        });
    }
}