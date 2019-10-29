<?php
/**
 * Created by PhpStorm.
 * User: v.sadovnikov
 * Date: 25.10.2019
 * Time: 16:19
 */

namespace App\Commands\ApiFetcher;


use App\Exceptions\EmptyDataException;
use App\Orm\Model\Archive;
use function GuzzleHttp\Psr7\parse_query;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 *Команда для забора данных из сторонних апи в бд.
 *
 * Class PullApiDataCommand
 *
 * @package App\Commands
 */
abstract class ApiDataFetcherCommand extends Command
{
    const DATE_FROM_MACROS = '{date_from}';
    const DATE_TO_MACROS = '{date_to}';
    
    /**
     * Урл запроса задается тут либо аргументом команды
     */
    protected $url;
    
    protected $archive;
    
    /**
     * @var InputInterface
     */
    protected $input;
    
    /**
     * @var OutputInterface
     */
    protected $output;
    
    protected $logger;
    
    /**
     * @var string
     */
    protected $requestPath;
    
    /**
     * @var array
     */
    protected $queryParams;
    
    /**
     * @var string
     */
    protected $method = 'GET';
    
    /**
     * @var array
     */
    protected $headers;
    
    /**
     * PullApiDataCommand constructor.
     *
     * @param Archive         $archive
     * @param LoggerInterface $logger
     */
    public function __construct(Archive $archive, LoggerInterface $logger)
    {
        $this->archive = $archive;
        $this->logger = $logger;
        parent::__construct($name = null);
    }
    
    protected function configure()
    {
        parent::configure();
        $this->addOption('url', null, InputOption::VALUE_OPTIONAL)
            ->addOption('period', null, InputOption::VALUE_OPTIONAL, 'Период в днях', 1)
            ->addOption('headers', null, InputOption::VALUE_OPTIONAL);
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;
        
        try {
            $this->process();
        } catch (EmptyDataException $emptyDataException) {
            $this->output->writeln([$emptyDataException->getMessage()]);
            exit();
        } catch (\Exception $exception) {
            $this->logger->error($exception->getMessage(), $exception->getTrace());
        }
    }
    
    /**
     * @throws EmptyDataException
     */
    protected function process()
    {
        if ($headers = $this->input->getOption('headers')) {
            $this->setHeaders(json_decode($headers, true));
        }
        
        $this->parseAndSetUrlParameters($this->getUrl());
        
        $response = $this->getApiResponse();
        
        if (empty($response)) {
            throw new EmptyDataException('Response is empty');
        }
        
        $this->saveResponseData($response);

    }
    
    abstract protected function getApiResponse();
    
    /**
     * @param $responseData
     *
     * @throws \Exception
     */
    protected function saveResponseData($responseData)
    {
        $this->output->writeln(['save response in archives']);
        $this->archive->saveLog($this->getName(), [$responseData]);
    }
    
    /**
     * @param $url
     *
     * @throws \Exception
     */
    protected function parseAndSetUrlParameters($url)
    {
        $url    = parse_url($url);
        
        if (!isset($url['path'])) {
            throw new \Exception('Url path is not definite');
        }
        
        $this->setRequestUri($url['scheme'].'://'.$url['host'].$url['path']);
        
        if (isset($url['query'])) {
            $query = parse_query($this->replaceDateMacroses($url['query']));
            $this->setQueryParams($query);
        }
    }
    
    /**
     * @param $query
     *
     * @return mixed
     * @throws \Exception
     */
    protected function replaceDateMacroses($query)
    {
        $date         = new \DateTime('now');
        $periodIndays = $this->input->getOption('period');
        $query        = str_replace(self::DATE_TO_MACROS, $date->format('Y-m-d'), $query);
        $query        = str_replace(self::DATE_FROM_MACROS, $date->modify("- {$periodIndays} days")->format('Y-m-d'), $query);
        
        return $query;
    }
    
    /**
     * @param mixed $headers
     */
    public function setHeaders($headers) : void
    {
        $this->headers = $headers;
    }
    
    /**
     * @param mixed $method
     */
    public function setMethod($method) : void
    {
        $this->method = $method;
    }
    
    /**
     * @param array $queryParams
     */
    public function setQueryParams($queryParams) : void
    {
        $this->queryParams = $queryParams;
    }
    
    /**
     * @param string $requestPath
     */
    public function setRequestUri($requestPath) : void
    {
        $this->requestPath = $requestPath;
    }
    
    /**
     * @return mixed
     */
    public function getHeaders()
    {
        return $this->headers;
    }
    
    /**
     * @return mixed
     */
    public function getMethod()
    {
        return $this->method;
    }
    
    /**
     * @return mixed
     */
    public function getQueryParams()
    {
        return $this->queryParams;
    }
    
    /**
     * @return mixed
     */
    public function getRequestPath()
    {
        return $this->requestPath;
    }
    
    /**
     * @param mixed $url
     */
    public function setUrl($url) : void
    {
        $this->url = $url;
    }
    
    /**
     * @return mixed
     */
    public function getUrl()
    {
        if (!$this->url) {
            return $this->input->getOption('url');
        }
        
        return $this->url;
    }
}