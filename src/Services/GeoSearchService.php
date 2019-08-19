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
    const MANDATORY_REQUEST_PARAMETERS = ['tool', 'parameters'];
    const TOOLS = ['geo', 'ali_orders'];
    
    const GEO_TYPE_WORLD_REGIONS = 1;
    const GEO_TYPE_WORLD_REGIONS_CODES = 2;
    const GEO_TYPE_RUSSIA_CITIES = 3;
    
    /**
     * @var Response
     */
    private $responseService;
    
    /**
     * @var string
     */
    private $requestData;
    
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    
    /**
     * GeoSearchService constructor.
     *
     * @param $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->responseService = new Response(
            '',
            Response::HTTP_OK,
            ['content-type' => 'text/html']
        );
        $this->requestData = $_POST['data'] ?: '';
    }
    
    public function sendResponse()
    {
        $data = $this->requestData;
        
        if (empty($data)) {
            $this->responseService->setStatusCode(Response::HTTP_BAD_REQUEST)->send();
            exit();
        }
        
        $data = json_decode($data, true);
        
        foreach (self::MANDATORY_REQUEST_PARAMETERS as $parameter) {
            if (!array_key_exists($parameter, $data)) {
                $this->responseService->setStatusCode(Response::HTTP_NOT_ACCEPTABLE)->send();
                exit();
            }
        }
        
        $response = $this->fetchGeoIds($data['parameters']['geoType'], $data['parameters']['geoList']);
        
        if ($response === null) {
            $response = 'Undefined geo...';
        }
        
        $this->responseService->setContent($response)->send();
    }
    
    /**
     * @param $geoType
     * @param $geoList
     */
    private function fetchGeoIds($geoType, $geoList)
    {
        $cityadsIds = [];
        
        switch ((int)$geoType) {
            case self::GEO_TYPE_WORLD_REGIONS:
                $repository = $this->entityManager->getRepository(CityadsWorldRegion::class);
                break;
            case self::GEO_TYPE_WORLD_REGIONS_CODES:
                $repository = $this->entityManager->getRepository(CityadsWorldRegionCodes::class);
                break;
            case self::GEO_TYPE_RUSSIA_CITIES:
                $repository = $this->entityManager->getRepository(CityadsCountryRussia::class);
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