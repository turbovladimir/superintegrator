<?php
/**
 * Created by PhpStorm.
 * User: v.sadovnikov
 * Date: 04.09.2019
 * Time: 11:10
 */

namespace App\Commands;


use App\Services\Superintegrator\PostbackCollector;


/**
 * Class CronCommand
 *
 * @package App\Commands
 */
class CollectPostbacksFromFilesCommand extends BaseDaemon
{
    protected static $defaultName = 'collect_postbacks';
    
    private $collector;
    
    /**
     * CollectPostbacksFromFilesCommand constructor.
     *
     * @param PostbackCollector $collector
     */
    public function __construct(PostbackCollector $collector)
    {
        $this->collector = $collector;
        parent::__construct();
    }
    
    /**
     *
     */
    protected function process()
    {
        $this->collector->start();
        $this->output->writeln('Postbacks successfully was be imported in messages');
    }
}