<?php
/**
 * Created by PhpStorm.
 * User: v.sadovnikov
 * Date: 04.09.2019
 * Time: 11:10
 */

namespace App\Commands;

use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\Factory\ServiceFactory;

/**
 * Class CronCommand
 *
 * @package App\Commands
 */
class CronCommand extends BaseDaemon
{
    protected static $defaultName = 'cron';
    
    private $services;
    private $logger;
    
    /**
     * CronCommand constructor.
     *
     * @param ServiceFactory  $factory
     * @param LoggerInterface $logger
     */
    public function __construct(ServiceFactory $factory, LoggerInterface $logger)
    {
        $this->logger   = $logger;
        $this->services = $factory->getServices();
        parent::__construct();
    }
    
    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        parent::initialize($input, $output);
        $output->write('get Services');
        
    }
    
    /**
     * @param InputInterface  $in
     * @param OutputInterface $out
     */
    protected function process()
    {
        foreach ($this->services as $service) {
            try {
                $service->start();
            } catch (\Exception $exception) {
                $this->logger->error($exception->getMessage(), $exception->getTrace());
            }
        }
    }
}