<?php
/**
 * Created by PhpStorm.
 * User: v.sadovnikov
 * Date: 19.08.2019
 * Time: 10:39
 */

namespace App\Services;

use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\CityadsCountryRussia;
use App\Entity\CityadsWorldRegion;
use App\Entity\CityadsWorldRegionCodes;

class GeoSearchService
{
    const GEO_TYPE_WORLD_REGIONS = 1;
    const GEO_TYPE_WORLD_REGIONS_CODES = 2;
    const GEO_TYPE_RUSSIA_CITIES = 3;
    
    /**
     * @var EntityManagerInterface
     */
    private static $entityManager;
    
    /**
     * @param EntityManagerInterface $entityManager
     * @param                        $parameters
     */
    public static function sendResponse(EntityManagerInterface $entityManager, $parameters)
    {
        self::$entityManager = $entityManager;
        $response = self::fetchGeoIds($parameters['geoType'], $parameters['geoList']);
        if ($response) {
            return new Response('Test', 200, ['content-type' => 'text/html']);
            //$responseService = new Response($response);
            //return $responseService;
        } else {
            return new Response('Some error', 403, ['content-type' => 'text/html']);
        }
    }
    
    /**
     * @param $geoType
     * @param $geoList
     *
     * @return false|string|null
     */
    private static function fetchGeoIds($geoType, $geoList)
    {
        $cityadsIds = [];
        
        switch ((int)$geoType) {
            case self::GEO_TYPE_WORLD_REGIONS:
                $repository = self::$entityManager->getRepository(CityadsWorldRegion::class);
                break;
            case self::GEO_TYPE_WORLD_REGIONS_CODES:
                $repository = self::$entityManager->getRepository(CityadsWorldRegionCodes::class);
                break;
            case self::GEO_TYPE_RUSSIA_CITIES:
                $repository = self::$entityManager->getRepository(CityadsCountryRussia::class);
                break;
        }
    
        $allGeo = $repository->findAll();
        $geoCount = count($allGeo);
    
        foreach ($geoList as $geoName) {
            $i = 0;
        
            while ($i < $geoCount) {
            
                if ($allGeo[$i]->getName() === $geoName) {
                    $cityadsIds['existing'][] = $allGeo[$i]->getCityadsId();
                    break;
                }
            
                $i++;
    
                // Собираем недостающее гео
                if ($i === $geoCount) {
                    $cityadsIds['missing'][] = $geoName;
                }
            }
        }

        return !empty($cityadsIds) ? json_encode($cityadsIds) : null;
    }
}