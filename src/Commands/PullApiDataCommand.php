<?php
/**
 * Created by PhpStorm.
 * User: v.sadovnikov
 * Date: 25.10.2019
 * Time: 16:19
 */

namespace App\Commands;


use App\Exceptions\EmptyDataException;
use App\Orm\Model\Archive;
use App\Services\Fetcher\ApiDataFetcher;
use function GuzzleHttp\Psr7\parse_query;
use Symfony\Component\Console\Input\InputOption;

/**
 * php bin/console data:pull --url='https://api.fonbetaffiliates.com/rpc/report/affiliate/dynamic-variables?affiliate=100379&filter[from]=#date_from#&filter[to]=#date_to#&groupBy=daily&page=1&count=100' --method='POST' --period=7 --headers='{"Accept":"application/json","Bearer":"433825e3f940e3d012c7082e510efb2141ff3f0a"}'
 *
 * Class PullApiDataCommand
 *
 * @package App\Commands
 */
class PullApiDataCommand extends BaseDaemon
{
    const DATE_FROM_MACROS = '{date_from}';
    const DATE_TO_MACROS = '{date_to}';
    const SOURCE_NAME = 'api_fetcher';
    
    
    protected static $defaultName = 'data:pull';
    
    private $fetcher;
    private $archive;
    
    /**
     * @param ApiDataFetcher $fetcher
     * @param Archive        $archive
     */
    public function __construct(ApiDataFetcher $fetcher, Archive $archive)
    {
        $this->fetcher = $fetcher;
        $this->archive = $archive;
        parent::__construct($name = null);
    }
    
protected function configure()
{
    parent::configure();
    $this->addOption('url',null , InputOption::VALUE_REQUIRED)
        ->addOption('method',null, InputOption::VALUE_OPTIONAL)
        ->addOption('period',null,InputOption::VALUE_OPTIONAL, 'Период в днях', 1)
        ->addOption('headers',null, InputOption::VALUE_OPTIONAL)
    ;
}
    
    protected function process()
    {
        $urlStr = $this->input->getOption('url');
        $url = parse_url($urlStr);
        
        if (!isset($url['path'])) {
            $this->output->writeln(['Url path is not definite']);
            return;
        }
        
        $this->fetcher->setRequestUri($url['scheme'] .'://'. $url['host'] . $url['path']);
        $this->fetcher->setMethod($this->input->getOption('method'));
        
        if (isset($url['query'])) {
            $query = parse_query($this->replaceDateMacroses($url['query']));
            $this->fetcher->setQueryParams($query);
        }
        
        if ($headers = $this->input->getOption('headers')) {
            $this->fetcher->setHeaders(json_decode($headers, true));
        }
        
        $response = $this->fetcher->getApiResponse();
        
        if (empty($response)) {
            throw new EmptyDataException('Response is empty');
        }
    
        $this->output->writeln(['save response in archives']);
        $this->archive->saveLog('api_fetcher', [$response]);
    }
    
    /**
     * @param $query
     *
     * @return mixed
     * @throws \Exception
     */
    private function replaceDateMacroses($query)
    {
        $date =  new \DateTime('now');
        $periodIndays = $this->input->getOption('period');
        $query = str_replace(self::DATE_TO_MACROS, $date->format('Y-m-d'), $query);
        $query = str_replace(self::DATE_FROM_MACROS, $date->modify("- {$periodIndays} days")->format('Y-m-d'), $query);
        
        return $query;
    }
    
    
}