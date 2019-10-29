<?php
/**
 * Created by PhpStorm.
 * User: v.sadovnikov
 * Date: 29.10.2019
 * Time: 16:10
 */

namespace App\Commands\Fonbet;


use App\Commands\ApiFetcher\FonbetApiDataFetcher;
use App\Commands\BaseDaemon;
use App\Orm\Model\Archive;
use App\Orm\Model\Message;
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
    
    private $messageModel;
    private $archiveModel;
    
    protected function configure()
    {
        parent::configure();
        
        $this->addArgument('source_name', InputOption::VALUE_REQUIRED, 'Выберите имя создателя лога');
    }
    
    /**
     * AffiseExportCommand constructor.
     *
     * @param Message $messageModel
     * @param Archive $archiveModel
     */
    public function __construct(Message $messageModel, Archive $archiveModel)
    {
        $this->messageModel = $messageModel;
        $this->archiveModel = $archiveModel;
        parent::__construct();
    }
    
    /**
     * @throws \Exception
     */
    protected function process()
    {
        $log = $this->archiveModel->getLog($this->input->getArgument('source_name'));
        $url = $this->createUrl($log);
        
        if ($url) {
            $this->messageModel->saveMessages($this->destination, $url);
        }
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
                ];
            }
        }
        
        if (isset($query)) {
            return $this->urlPath.'?'.http_build_query($query);
        }
        
        return null;
    }
}