<?php
/**
 * Created by PhpStorm.
 * User: v.sadovnikov
 * Date: 19.08.2019
 * Time: 10:39
 */

namespace App\Services\Superintegrator;

use App\Response\AlertMessageCollection;
use App\Orm\Entity\Superintegrator\CountryRussia;
use App\Orm\Entity\Superintegrator\WorldRegion;
use App\Orm\Entity\Superintegrator\WorldRegionCodes;
use App\Services\AbstractService;
use App\Utils\StringHelper;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class GeoSearchService
 *
 * @package App\Services\Superintegrator
 */
class GeoSearchService extends AbstractService
{
    private const GEO_TYPE_WORLD_REGIONS = 1;
    private const GEO_TYPE_WORLD_REGIONS_CODES = 2;
    private const GEO_TYPE_RUSSIA_CITIES = 3;
    
    /**
     * @param Request $request
     *
     * @return AlertMessageCollection| null
     */
    public function processRequest(Request $request)
    {
        parse_str($request->getContent(), $parameters);
        
        if (!$parameters || empty($parameters['geo_type']) || empty($parameters['list'])) {
            return null;
        }
    
        $geoArray = StringHelper::splitId($parameters['list']);
    
        if (!$geoArray) {
            return null;
        }
    
        switch ((int)$parameters['geo_type']) {
            case self::GEO_TYPE_WORLD_REGIONS:
                $repository = $this->entityManager->getRepository(WorldRegion::class);
                break;
            case self::GEO_TYPE_WORLD_REGIONS_CODES:
                $repository = $this->entityManager->getRepository(WorldRegionCodes::class);
                break;
            case self::GEO_TYPE_RUSSIA_CITIES:
                $repository = $this->entityManager->getRepository(CountryRussia::class);
                break;
            default:
                return null;
        }
    
        $allGeo = $repository->findAll();
        $geoCount = count($allGeo);
        $cityadsIds = [];
        
        foreach ($geoArray as $geoName) {
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

        $responseMessage = new AlertMessageCollection();
        empty($cityadsIds['existing']) ?:
        $responseMessage->addAlert('Successful found', implode(',', $cityadsIds['existing']));
        empty($cityadsIds['missing']) ?:
            $responseMessage->addAlert('Not founded', implode(',', $cityadsIds['missing']), AlertMessageCollection::ALERT_TYPE_DANGER);
        
        return $responseMessage;
    }
}