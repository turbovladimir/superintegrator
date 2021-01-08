<?php


namespace App\Response;

class ResponseXmlPage extends ResponseData
{

    public function __construct(string $xml) {
        $this->data[] = $xml;
    }
}