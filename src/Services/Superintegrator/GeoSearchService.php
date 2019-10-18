<?php
/**
 * Created by PhpStorm.
 * User: v.sadovnikov
 * Date: 19.08.2019
 * Time: 10:39
 */

namespace App\Services\Superintegrator;

use App\Entity\Superintegrator\CountryRussia;
use App\Entity\Superintegrator\WorldRegion;
use App\Entity\Superintegrator\WorldRegionCodes;
use App\Services\AbstractService;

/**
 * Class GeoSearchService
 *
 * @package App\Services\Superintegrator
 */
class GeoSearchService extends AbstractService
{
    const GEO_TYPE_WORLD_REGIONS = 1;
    const GEO_TYPE_WORLD_REGIONS_CODES = 2;
    const GEO_TYPE_RUSSIA_CITIES = 3;
    
    /**
     * @param $parameters
     *
     * @return false|string|null
     */
    public function process($parameters)
    {
        return $this->fetchGeoIds($parameters['geoType'], $parameters['geoList']);
    }
    
    /**
     * @param $geoType
     * @param $geoList
     *
     * @return string|null
     */
    private function fetchGeoIds($geoType, $geoList)
    {
        $cityadsIds = [];
        
        switch ((int)$geoType) {
            case self::GEO_TYPE_WORLD_REGIONS:
                $repository = $this->entityManager->getRepository(WorldRegion::class);
                break;
            case self::GEO_TYPE_WORLD_REGIONS_CODES:
                $repository = $this->entityManager->getRepository(WorldRegionCodes::class);
                break;
            case self::GEO_TYPE_RUSSIA_CITIES:
                $repository = $this->entityManager->getRepository(CountryRussia::class);
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