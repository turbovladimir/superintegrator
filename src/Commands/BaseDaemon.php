<?php
/**
 * Created by PhpStorm.
 * User: v.sadovnikov
 * Date: 03.09.2019
 * Time: 18:27
 */

namespace App\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;

abstract class BaseDaemon extends Command
{
    const DEFAULT_LIFETIME = 600;
    const WORKER_LIFETIME = 20;
    const WORERS_DEFAULT_COUNT = 10;
    
    private $workersCount = self::WORERS_DEFAULT_COUNT;
    
    private $workers = [];
    
    protected $output;
    
    protected function configure()
    {
        $this
            ->setDescription('Команда базового демона')
            ->setHelp('Помощи ждать неоткуда')
        ;
        
        $this->addOption('daemonMode',      null, InputOption::VALUE_REQUIRED, 'Запуск в режиме демона', false);
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;
        // outputs multiple lines to the console (adding "\n" at the end of each line)
        $output->writeln([
            'Демон просыпается',
            '============',
            '',
        ]);
        
        $this->start($input, $output);
        
        $output->write('И ложится спать');
        exit();
    }
    
    private function start(InputInterface $in, OutputInterface $out)
    {
        $daemonMode = (bool)$in->getOption('daemonMode');
        $startedAt = time();
        
        while (true) {
            
            if (self::DEFAULT_LIFETIME < (time() - $startedAt)) {
                //todo надо доделать для мультипоточности
                //$this->stop();
                break;
            }
    
            //todo зарезолвить екстеншен для пхп
            if ($daemonMode && extension_loaded('pcntl')) {
                $this->startMultiThread();
            } else {
                $this->gainServiceMethods();
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
    
            $this->workersCount --;
        }

    }
    
    private function processLoop()
    {
        $workerStartTime = time();
        
        while (self::WORKER_LIFETIME < (time() - $workerStartTime)) {
            sleep(1);
            $this->gainServiceMethods();
        }
        
        exit();
    }
    
    abstract protected function gainServiceMethods();
    
    
    
}