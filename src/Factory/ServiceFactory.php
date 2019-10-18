<?php
/**
 * Created by PhpStorm.
 * User: v.sadovnikov
 * Date: 17.10.2019
 * Time: 16:59
 */

namespace App\Factory;

use App\Services\CitySenderService;
use App\Services\OmarsysDataAggregator;

/**
 * Class ServiceFactory
 *
 * @package App\Factory
 */
class ServiceFactory
{
    /**
     * @var array
     */
    private $services;
    
    /**
     * ServiceFactory constructor.
     *
     * @param CitySenderService     $sender
     * @param OmarsysDataAggregator $aggregator
     */
    public function __construct(CitySenderService $sender, OmarsysDataAggregator $aggregator)
    {
        $this->services =  func_get_args();
    }
    
    /**
     * @return array
     */
    public function getServices() : array
    {
        return $this->services;
    }
}