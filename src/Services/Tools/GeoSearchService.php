<?php
/**
 * Created by PhpStorm.
 * User: v.sadovnikov
 * Date: 19.08.2019
 * Time: 10:39
 */

namespace App\Services\Tools;

use App\Response\ResponseData;
use App\Response\ResponseMessage;
use App\Entity\Superintegrator\CountryRussia;
use App\Entity\Superintegrator\WorldRegion;
use App\Entity\Superintegrator\WorldRegionCodes;
use App\Utils\StringHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Class GeoSearchService
 *
 * @package App\Services\Tools
 */
class GeoSearchService implements Tool
{
    private const GEO_TYPE_WORLD_REGIONS = 1;
    private const GEO_TYPE_WORLD_REGIONS_CODES = 2;
    private const GEO_TYPE_RUSSIA_CITIES = 3;

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
    }

    public function getToolInfo(): array {
        return [
            'name' => 'geo',
            'title' => 'Geo searching',
            'description' => 'Инструмент для поиска id гео объектов (страны, регионы, города), для получения id можно использовать как 2х буквенные коды стран так и полные ENG наименования'
        ];
    }

    /**
     * @inheritDoc
     */
    public function process(array $parameters, $action = null) {
        if (!$action) {
            return null;
        }

        if (!$parameters ||
            empty($parameters['action']) ||
            empty($parameters['list']) ||
            !($geoArray = StringHelper::splitId($parameters['list']))) {
            throw new BadRequestHttpException('Empty or incorrect geo list');
        }

        $typeId = (int)substr($parameters['action'], -1, 1);
    
        switch ($typeId) {
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
        
        if (empty($allGeo)) {
            throw new BadRequestHttpException('No data with geo in database');
        }
        
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

        $responseMessage = new ResponseMessage();
        empty($cityadsIds['existing']) ?:
        $responseMessage->addInfo('Successful found', implode(',', $cityadsIds['existing']));
        empty($cityadsIds['missing']) ?:
            $responseMessage->addError('Not founded', implode(',', $cityadsIds['missing']));
        
        return $responseMessage;
    }
}