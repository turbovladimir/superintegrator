<?php
/**
 * Created by PhpStorm.
 * User: v.sadovnikov
 * Date: 25.10.2019
 * Time: 15:39
 */

namespace App\Services\Fetcher;


use App\Exceptions\ExpectedException;
use GuzzleHttp\Client;

class ApiDataFetcher
{
    
    /**
     * @var Client
     */
    private $client;
    /**
     * @var string
     */
    private $requestPath;
    
    /**
     * @var array
     */
    private $queryParams;
    
    /**
     * @var string
     */
    private $method = 'GET';
    
    /**
     * @var array
     */
    private $headers;
    
    /**
     * ApiDataFetcher constructor.
     *
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }
    
    /**
     * @return string
     * @throws ExpectedException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function getApiResponse() {
        $options = [];
        
        if (!$this->getRequestPath()) {
            throw new ExpectedException('Request path not set');
        }
    
        $this->getHeaders() ? $options['headers'] = $this->getHeaders() : null;
        $this->getQueryParams() ? $options['query'] = $this->getQueryParams() : null;
        $response = $this->client->request($this->getMethod(), $this->getRequestPath(), $options)->getBody();
            
        return $response->getContents();
    }
    
    /**
     * @param mixed $headers
     */
    public function setHeaders($headers) : void
    {
        $this->headers = $headers;
    }
    
    /**
     * @param mixed $method
     */
    public function setMethod($method) : void
    {
        $this->method = $method;
    }
    
    /**
     * @param array $queryParams
     */
    public function setQueryParams($queryParams) : void
    {
        $this->queryParams = $queryParams;
    }
    
    /**
     * @param string $requestPath
     */
    public function setRequestPath($requestPath) : void
    {
        $this->requestPath = $requestPath;
    }
    
    /**
     * @return mixed
     */
    public function getHeaders()
    {
        return $this->headers;
    }
    
    /**
     * @return mixed
     */
    public function getMethod()
    {
        return $this->method;
    }
    
    /**
     * @return mixed
     */
    public function getQueryParams()
    {
        return $this->queryParams;
    }
    
    /**
     * @return mixed
     */
    public function getRequestPath()
    {
        return $this->requestPath;
    }
    
}