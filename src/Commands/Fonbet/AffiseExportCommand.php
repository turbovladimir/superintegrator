<?php
/**
 * Created by PhpStorm.
 * User: v.sadovnikov
 * Date: 29.10.2019
 * Time: 16:10
 */

namespace App\Commands\Fonbet;

use App\Commands\BaseDaemon;
use App\Orm\Model\Archive;
use App\Repository\ArchiveRepository;
use App\Repository\MessageRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Input\InputOption;

/**
 * Команда формирующая постбэки для аффайз, в афайзе есть защита от дублирования данных поэтому можно отправлять дубли
 *
 * Class AffiseExportCommand
 *
 * @package App\Commands\Fonbet
 */
class AffiseExportCommand extends BaseDaemon
{
    private const HASH_CODE = '287d778ca096773adcc2cb1e4c62ed2f';
    
    protected static $defaultName = 'affise_export';
    protected $destination = 'adsbang.affise.com';
    protected $urlPath = 'http://offers.adsbang.affise.com/postback';
    
    private $messageRepository;
    private $archiveRepository;
    
    protected function configure()
    {
        parent::configure();
        
        $this->addArgument('source_name', InputOption::VALUE_REQUIRED, 'Выберите имя создателя лога');
    }
    
    /**
     * AffiseExportCommand constructor.
     *
     * @param LoggerInterface   $logger
     * @param MessageRepository $messageRepository
     * @param ArchiveRepository $archiveRepository
     */
    public function __construct(LoggerInterface $logger, MessageRepository $messageRepository,ArchiveRepository $archiveRepository)
    {
        $this->messageRepository = $messageRepository;
        $this->archiveRepository = $archiveRepository;
        parent::__construct($logger);
    }
    
    /**
     * @inheritDoc
     */
    protected function process() : bool
    {
        $log = $this->archiveRepository->findOneBy(['source' => $this->input->getArgument('source_name')]);
        $url = $this->createUrl($log->getLogData());
        $em = $this->archiveRepository->getEntityManager();
        $em->remove($log);
        $em->flush();
        
        if ($url) {
            $this->messageRepository->saveMessages($this->destination, $url);
        }
        
        return true;
    }
    
    /**
     * @param $log
     *
     * @return string|null
     */
    protected function createUrl($log)
    {
        $logData = json_decode($log, true);
        
        foreach ($logData['_embedded'] as $item) {
            if ($item['registrations'] !== 0) {
                $query = [
                    'referrer' => $item['channelId'],
                    'clickid'  => $item['parameter'],
                    'secure'  => self::HASH_CODE,
                    'goal'  => 'reg',
                ];
            }
        }
        
        if (isset($query)) {
            return $this->urlPath.'?'.http_build_query($query);
        }
        
        return null;
    }
}