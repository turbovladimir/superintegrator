<?php
/**
 * Created by PhpStorm.
 * User: v.sadovnikov
 * Date: 18.10.2019
 * Time: 17:15
 */

namespace App\Commands;

use App\Services\Superintegrator\PostbackCollector;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;

class TestCommand extends Command
{
    protected static $defaultName = 'test';
    
    /**
     * @var PostbackCollector
     */
    private $collector;
    
    
    public function __construct(PostbackCollector $collector)
    {
        parent::__construct($name = null);
        $this->collector = $collector;
    }
    
    protected function configure()
    {
        $this
            ->setDescription('Команда тестов')
            ->setHelp('Помогает при тестах сервисов... иногда))')
        ;
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->collector->start();
    }
}