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
    const WORKER_LIFETIME = 20;
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
        // outputs multiple lines to the console (adding "\n" at the end of each line)
        $output->writeln(
            [
                'Демон просыпается',
                '============',
                '',
            ]
        );
        
        $this->start();
        
        $output->write('И ложится спать');
    }
    
    private function start()
    {
        $daemonMode = (bool) $this->input->getOption('daemonMode');
        $startedAt  = time();
        
        while (true) {
            try {
                if (self::DEFAULT_LIFETIME < (time() - $startedAt)) {
                    //todo надо доделать для мультипоточности
                    //$this->stop();
                    break;
                }
                
                //todo зарезолвить екстеншен для пхп
                if ($daemonMode && extension_loaded('pcntl')) {
                    $this->startMultiThread();
                } else {
                    $this->process();
                }
            } catch (EmptyDataException $emptyDataException) {
                $this->output->writeln([$emptyDataException->getMessage()]);
                exit();
            } catch (\Exception $exception) {
                $this->logger->error($exception->getMessage(), $exception->getTrace());
            }
        }
    }
    
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
    
    private function processLoop()
    {
        $workerStartTime = time();
        $stopFlag = false;
        
        while (self::WORKER_LIFETIME < (time() - $workerStartTime) && !$stopFlag) {
            sleep(1);
            $stopFlag = $this->process();
        }
        
        exit();
    }
    
    abstract protected function process();
    
    
}