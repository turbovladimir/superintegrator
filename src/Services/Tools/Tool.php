<?php


namespace App\Services\Tools;


use App\Response\ResponseData;
use Symfony\Component\HttpFoundation\Request;

interface Tool
{
    /**
     * @return array
     */
    public function getToolInfo() : array;

    /**
     * @param array $parameters
     * @param string|null $action
     * @return ResponseData|null
     * @throws
     */
    public function process(array $parameters, $action = null);
}