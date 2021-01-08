<?php


namespace App\Response;


abstract class ResponseData
{
    /**
     * @var array
     */
    protected $data = [];

    public function getData() : array {
        return  $this->data;
    }
}