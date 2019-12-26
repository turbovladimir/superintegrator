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


/**
 * Class PostbackImportCommand
 *
 * @package App\Commands
 */
class PostbackImportCommand extends BaseDaemon
{
    protected static $defaultName = 'collect_postbacks';
    protected $description = 'Вытасивает из архивных файлов и ставит в очередь постбэки';
    
    private $collector;
    private $messageRepository;
    
    /**
     * PostbackImportCommand constructor.
     *
     * @param CityadsPostbackManager $collector
     * @param MessageRepository      $messageRepository
     */
    public function __construct(CityadsPostbackManager $collector, MessageRepository $messageRepository)
    {
        $this->collector = $collector;
        $this->messageRepository = $messageRepository;
        parent::__construct();
    }
    
    /**
     * @throws \Throwable
     */
    protected function process()
    {
        $this->messageRepository->getEntityManager()->transactional(function () {
            $urls = $this->collector->getUrls();
            $this->output->writeln('Getting '. count($urls) . ' urls from files');
            $this->messageRepository->saveMessages(CityadsPostbackManager::DESTINATION, $urls);
            $this->output->writeln('Postbacks successfully was be imported in messages');
        });
    }
}