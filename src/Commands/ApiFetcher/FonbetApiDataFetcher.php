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
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 *  * php bin/console api_data:pull:omarsys
 * --url='https://api.fonbetaffiliates.com/rpc/report/affiliate/dynamic-variables?affiliate=100379&filter[from]={date_from}&filter[to]={date_to}&groupBy=daily&page=1&count=100' --method='POST' --period=60
 *
 * Class OmarsysFetcher
 *
 * @package App\Commands\ApiFetcher
 */
class FonbetApiDataFetcher extends ApiDataFetcherCommand
{
    const CLIENT_SECRET = 'cha44liates2014';
    const API_HOST_NAME = 'https://api.fonbetaffiliates.com/';
    
    const RU_URL = self::API_HOST_NAME . 'rpc/report/affiliate/dynamic-variables?affiliate=100379&filter[from]={date_from}&filter[to]={date_to}&groupBy=daily&page=1&count=100';
    const KZ_URL = self::API_HOST_NAME . 'rpc/report/affiliate/dynamic-variables?affiliate=100380&filter[from]={date_from}&filter[to]={date_to}&groupBy=daily&page=1&count=100';
    
    protected static $defaultName = 'api_data:fonbet';
    
    /**
     * @var string
     */
    protected $method = 'POST';
    
    protected function configure()
    {
        parent::configure();
        
        $this->addArgument('offer', InputOption::VALUE_REQUIRED, 'Необходимо задать два значения : ru|kz для забора с двух апи');
    }
    
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $token = $this->generateToken();
        $this->setHeaders(["Authorization: Bearer {$token}"]);
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $input->getArgument('offer') === 'ru' ? $this->setUrl(self::RU_URL) : $this->setUrl(self::KZ_URL);
        parent::execute($input, $output);
    }
    
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
            self::API_HOST_NAME . 'oauth',
            [
                'form_params' => [
                    'username'      => $this->input->getArgument('offer') === 'ru' ? 'germanru' : 'germankz',
                    'password'      => 'german777',
                    'client_id'     => 'omarsys',
                    'client_secret' => self::CLIENT_SECRET,
                    'grant_type'    => 'password',
                ]
            ]
        )->getBody()->getContents();
        
        return json_decode($response, true)['access_token'];
    }
}