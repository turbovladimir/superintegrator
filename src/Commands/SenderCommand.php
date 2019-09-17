<?php
/**
 * Created by PhpStorm.
 * User: v.sadovnikov
 * Date: 04.09.2019
 * Time: 11:10
 */

namespace App\Commands;

use App\Services\CitySenderService;

class SenderCommand extends BaseDaemon
{
    const COMMAND_NAME = 'sender';
    
    private $service;
    
    public function __construct(CitySenderService $service)
    {
        $this->service = $service;
        parent::__construct(self::COMMAND_NAME);
    }
    
    protected function gainServiceMethods()
    {
        $this->service->clearDb();
        $this->service->sendFromDb();
    }
    
}