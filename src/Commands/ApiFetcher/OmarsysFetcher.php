<?php
/**
 * Created by PhpStorm.
 * User: v.sadovnikov
 * Date: 28.10.2019
 * Time: 13:26
 */

namespace App\Commands\ApiFetcher;

use GuzzleHttp\Client;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 *  * php bin/console api_data:pull:omarsys
 * --url='https://api.fonbetaffiliates.com/rpc/report/affiliate/dynamic-variables?affiliate=100379&filter[from]={date_from}&filter[to]={date_to}&groupBy=daily&page=1&count=100' --method='POST' --period=60
 *
 * Class OmarsysFetcher
 *
 * @package App\Commands\ApiFetcher
 */
class OmarsysFetcher extends ApiDataFetcherCommand
{
    const CLIENT_SECRET = 'cha44liates2014';
    const API_HOST_NAME = 'api.fonbetaffiliates.com';
    
    protected static $defaultName = 'api_data:pull:omarsys';
    
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $token = $this->generateToken();
        $this->setHeaders(["Authorization: Bearer {$token}"]);
    }
    
    /**
     * @var string
     */
    protected $method = 'POST';
    
    protected function getApiResponse()
    {
        $curl = curl_init();
        
        curl_setopt_array(
            $curl,
            [
                CURLOPT_URL            => $this->getRequestPath().'?'.http_build_query($this->getQueryParams()),
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING       => '',
                CURLOPT_MAXREDIRS      => 10,
                CURLOPT_TIMEOUT        => 30,
                CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST  => $this->getMethod(),
                CURLOPT_HTTPHEADER     => $this->getHeaders(),
            ]
        );
        
        $response = curl_exec($curl);
        $err      = curl_error($curl);
        
        curl_close($curl);
        
        if ($err) {
            throw new \Exception("cURL Error #:".$err);
        }
        
        return $response;
    }
    
    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function generateToken()
    {
        $client   = new Client();
        $response = $client->request(
            $this->getMethod(),
            self::API_HOST_NAME . '/oauth',
            [
                'form_params' => [
                    'username'      => 'germanru',
                    'password'      => 'german777',
                    'client_id'     => 'omarsys',
                    'client_secret' => self::CLIENT_SECRET,
                    'grant_type'    => 'password',
                ],
            ]
        )->getBody()->getContents();
        
        return json_decode($response, true)['access_token'];
    }
}