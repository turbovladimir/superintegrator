<?php
/**
 * Created by PhpStorm.
 * User: v.sadovnikov
 * Date: 17.09.2019
 * Time: 14:40
 */

namespace App\Services;

use \GuzzleHttp\Client;
use App\Exceptions\ExpectedException;
use App\Entity\Fonbet\PublishersStatistic;

class OmarsysDataAggregator extends AbstractService
{
    private const TOKEN = '433825e3f940e3d012c7082e510efb2141ff3f0a';
    private const AFFILATE_ID = 100379;
    private const REQUEST_PATH = 'https://api.fonbetaffiliates.com/rpc/report/variables';
    private const REQUEST_METHOD = 'GET';
    
    public function process($parameters)
    {
        $data = $this->getData();
        $publishers = $this->aggregateData($data);
        
        if (empty($publishers)) {
            return null;
        }
        
        foreach ($publishers as $publisher) {
            $entityPublisherStatistic = new PublishersStatistic();
            $entityPublisherStatistic->setClickId($publisher['clickId']);
            $entityPublisherStatistic->setWmId($publisher['wmId']);
            $entityPublisherStatistic->setRegistrations($publisher['registrations']);
            $entityPublisherStatistic->setDepositsAmount($publisher['deposits']);
            $this->entityManager->persist($entityPublisherStatistic);
            $this->entityManager->flush();
        }
    }
    
    private function aggregateData($data)
    {
        if (empty($data['_embedded'])) {
            return null;
        }
    
        $publishers = [];
        
        foreach ($data['_embedded'] as $item) {
            if ($item['registrations'] !== 0) {
                $publishers[] = [
                    'wmId' => $item['channelId'],
                    'clickId' => $item['parameter'],
                    'registrations' => $item['registrations'],
                    'deposits' => $item['deposits'],
                    ];
            }
        }
        
        return $publishers;
    }
    
    private function getData()
    {
        
        $dateFrom = date('Y-m-d', time() - 60 * 60 * 24);
        $dateTo = date('Y-m-d', time() + 60 * 60 * 24);
        $url      = self::REQUEST_PATH;
        $query    = [
            'affiliate'    => self::AFFILATE_ID,
            //todo поправить
            'filter[from]' => '2019-08-01',
            'filter[to]'   => $dateTo,
        ];
        $headerParams = [
            'Authorization' => 'Bearer '. self::TOKEN,
            'Accept'        => 'application/json',
        ];
        
        try{
            $client   = new Client();
            $response = $client->request(self::REQUEST_METHOD, $url, ['headers' => $headerParams, 'query' => $query])->getBody();
            $json     = $response->getContents();
        } catch (\Exception $exception) {
            throw new ExpectedException("[{".get_class($exception)."}]". $exception->getMessage());
        }
        
        return json_decode($json, true);
    }
}