<?php
/**
 * Created by PhpStorm.
 * User: v.sadovnikov
 * Date: 17.09.2019
 * Time: 14:40
 */

namespace App\Services\Fonbet;

use App\Exceptions\ExpectedException;
use App\Orm\Entity\Fonbet\PublishersStatistic;
use App\Services\Fetcher\ApiDataFetcher;
use App\Services\TaskServiceInterface;

class OmarsysDataAggregator
{
    private const TOKEN = '433825e3f940e3d012c7082e510efb2141ff3f0a';
    private const AFFILATE_ID = 100379;
    private const REQUEST_PATH = 'https://api.fonbetaffiliates.com/rpc/report/variables';

    /**
     * @return mixed|null
     * @throws ExpectedException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function start()
    {
        $data = $this->getData();
        $publishers = $this->aggregateData($data);

        if (empty($publishers)) {
            return null;
        }

        $this->pushToDb($publishers);
    }

    /**
     * @param $publishers
     */
    protected function pushToDb($publishers) {
        foreach ($publishers as $publisher) {
            $entityPublisherStatistic = new PublishersStatistic();
            $entityPublisherStatistic->setClickId($publisher['clickId']);
            $entityPublisherStatistic->setWmId($publisher['wmId']);
            $entityPublisherStatistic->setRegistrations($publisher['registrations']);
            $entityPublisherStatistic->setDepositsAmount($publisher['deposits']);
            $this->entityManager->persist($entityPublisherStatistic);

        }

        $this->entityManager->flush();
    }


    public function process($parameters)
    {
    }

    /**
     * @param $data
     *
     * @return array|null
     */
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

    /**
     * @return mixed
     * @throws ExpectedException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function getData()
    {
        $date = new \DateTime('now');

        $dateFrom = $date->modify('-1 day');
        $dateTo = $date->modify('+1 day');
        $this->setRequestPath(self::REQUEST_PATH);
        $this->setQueryParams([
            'affiliate'    => self::AFFILATE_ID,
            'filter[from]' => $dateFrom->format('Y-m-d'),
            'filter[to]'   => $dateTo->format('Y-m-d'),
        ]);
        $this->setHeaders([
            'Authorization' => 'Bearer '. self::TOKEN,
            'Accept'        => 'application/json',
        ]);



        return json_decode($this->getApiResponse(), true);
    }
}