<?php


namespace App\Response;


class ResponsePageParameters extends ResponseData
{
    public function __construct(array $data) {
        $this->data = $data;
    }
}