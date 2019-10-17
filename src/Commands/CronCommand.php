<?php
/**
 * Created by PhpStorm.
 * User: v.sadovnikov
 * Date: 04.09.2019
 * Time: 11:10
 */

namespace App\Commands;

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
    
    public function __construct(ServiceFactory $factory)
    {
        $this->services = $factory->getServices();
        parent::__construct();
    }
    
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        parent::initialize($input, $output);
        $output->write('get Services');
        
    }
    
    protected function gainServiceMethods()
    {
        foreach ($this->services as $service) {
            $service->start();
        }
    }
}