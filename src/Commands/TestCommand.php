<?php
/**
 * Created by PhpStorm.
 * User: v.sadovnikov
 * Date: 18.10.2019
 * Time: 17:15
 */

namespace App\Commands;

use App\Services\Sender\Sender;
use App\Services\Superintegrator\PostbackCollector;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\ExpressionLanguage\Tests\Node\AbstractNodeTest;

class TestCommand extends Command
{
    const COLLECTOR = 'collector';
    const SENDER = 'sender';
    
    protected static $defaultName = 'test';
    
    /**
     * @var PostbackCollector
     */
    private $collector;
    
    /**
     * @var Sender
     */
    private $sender;
    
    
    public function __construct(PostbackCollector $collector, Sender $sender)
    {
        parent::__construct($name = null);
        $this->collector = $collector;
        $this->sender = $sender;
    }
    
    protected function configure()
    {
        $this
            ->setDescription('Команда тестов')
            ->setHelp('Помогает при тестах сервисов... иногда))')
            ->addOption('service', null, InputOption::VALUE_REQUIRED, 'select service for test')
        ;
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($input->getOption('service') === self::COLLECTOR) {
            $this->collector->start();
        }
    
        if ($input->getOption('service') === self::SENDER) {
            $this->sender->start();
        }
    }
}