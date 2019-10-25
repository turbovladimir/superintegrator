<?php
/**
 * Created by PhpStorm.
 * User: v.sadovnikov
 * Date: 04.09.2019
 * Time: 11:10
 */

namespace App\Commands;

use App\Exceptions\ExpectedException;
use App\Services\Sender\Sender;

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
     * @param Sender $sender
     */
    public function __construct(Sender $sender)
    {
        $this->sender = $sender;
        parent::__construct($name = null);
    }
    
    protected function process()
    {
        $this->sender->start();
    }
}