<?php
/**
 * Created by PhpStorm.
 * User: v.sadovnikov
 * Date: 04.09.2019
 * Time: 11:10
 */

namespace App\Commands;

use App\Services\Sender\MessageSender;

/**
 * Class CronCommand
 *
 * @package App\Commands
 */
class SenderCommand extends BaseDaemon
{
    protected $defaultName = 'sender';
    
    private $sender;
    
    /**
     * SenderCommand constructor.
     *
     * @param MessageSender $sender
     */
    public function __construct(MessageSender $sender)
    {
        $this->sender = $sender;
        parent::__construct($name = null);
    }
    
    protected function process()
    {
        $this->sender->start();
    }
}