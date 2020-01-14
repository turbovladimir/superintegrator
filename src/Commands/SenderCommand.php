<?php
/**
 * Created by PhpStorm.
 * User: v.sadovnikov
 * Date: 04.09.2019
 * Time: 11:10
 */

namespace App\Commands;

use App\Services\Sender\MessageSender;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Input\InputOption;

/**
 * Class CronCommand
 *
 * @package App\Commands
 */
class SenderCommand extends BaseDaemon
{
    protected static $defaultName = 'sender';
    
    private $sender;
    
    /**
     * SenderCommand constructor.
     *
     * @param LoggerInterface $logger
     * @param MessageSender   $sender
     */
    public function __construct(LoggerInterface $logger, MessageSender $sender)
    {
        $this->sender = $sender;
        parent::__construct($logger);
    }
    
    /**
     *
     */
    protected function configure()
    {
        parent::configure();
        
        $this->addOption('delete_after_sending', null,InputOption::VALUE_OPTIONAL);
    }
    
    /**
     * @throws \App\Exceptions\EmptyDataException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    protected function process() : void
    {
        $this->sender->send((bool)$this->input->getOption('delete_after_sending'));
    }
}