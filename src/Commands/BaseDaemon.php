<?php
/**
 * Created by PhpStorm.
 * User: v.sadovnikov
 * Date: 03.09.2019
 * Time: 18:27
 */

namespace App\Commands;

use App\Exceptions\EmptyDataException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;

abstract class BaseDaemon extends Command
{
    
    protected static $defaultName = 'daemon';
    protected $description = 'Команда базового демона';
    
    const DEFAULT_LIFETIME = 600;
    const WORERS_DEFAULT_COUNT = 10;
    
    private $workersCount = self::WORERS_DEFAULT_COUNT;
    
    private $workers = [];
    
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
     * Основной метод в котором происходит выполнение логики дочерних команд
     */
    abstract protected function process() : void ;
    
    /**
     * BaseDaemon constructor.
     *
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        parent::__construct($name = null);
        $this->logger = $logger;
    }
    
    /**
     *
     */
    protected function configure()
    {
        $this
            ->setDescription($this->description)
            ->setHelp('');
        
        $this->addOption('daemonMode', null, InputOption::VALUE_OPTIONAL, 'Запуск в режиме демона', 0);
    }
    
    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|void|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;
        $this->input  = $input;
        $daemonMode   = (bool) $this->input->getOption('daemonMode');
        
        $this->logger->info('Start executing '.$this->getName());
        try {
            //todo зарезолвить екстеншен для пхп
            if ($daemonMode && extension_loaded('pcntl')) {
                $this->startMultiThread();
            } else {
                $this->processLoop();
            }
        } catch (\Exception $exception) {
            $this->logger->error($exception->getMessage(), $exception->getTrace());
            exit();
        }
        
    }
    
    /**
     *
     */
    private function startMultiThread()
    {
        while ($this->workersCount) {
            $pid = pcntl_fork();
            
            if ($pid === -1) {
                continue;
            } elseif ($pid > 0) {
                $this->workers[] = $pid;
            } else {
                $this->processLoop();
            }
            
            $this->workersCount--;
        }
        
    }
    
    /**
     *
     */
    private function processLoop()
    {
        $workerStartTime = time();
        
        while (self::DEFAULT_LIFETIME >= (time() - $workerStartTime)) {
            $this->process();
            sleep(5);
        }
        
        exit();
    }
}