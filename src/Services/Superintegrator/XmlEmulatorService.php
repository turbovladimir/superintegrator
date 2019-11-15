<?php
/**
 * Created by PhpStorm.
 * User: v.sadovnikov
 * Date: 21.08.2019
 * Time: 19:24
 */

namespace App\Services\Superintegrator;

use App\Forms\ResponseMessage\AlertMessageCollection;
use App\Orm\Entity\Superintegrator\TestXml;
use App\Exceptions\ExpectedException;
use App\Services\AbstractService;
use Symfony\Component\HttpFoundation\Request;

class XmlEmulatorService extends AbstractService
{
    /**
     * @param Request $request
     *
     * @return AlertMessageCollection
     * @throws ExpectedException
     */
    public function create(Request $request)
    {
        parse_str($request->getContent(), $parameters);
        
        if (empty($parameters['new_xml'])) {
            return null;
        }
        
        $xmlstr = $parameters['new_xml'];
        libxml_use_internal_errors(true);
        $doc = simplexml_load_string($xmlstr);
        
        if (!$doc) {
            throw new ExpectedException('Invalid xml. Errors: '.implode("\n", libxml_get_errors()));
        }
        
        $entityXml = new TestXml();
        $key       = $this->generateHashKey($xmlstr);
        $entityXml->setXml($xmlstr);
        $entityXml->setUrl('http://'.$_SERVER['HTTP_HOST'].'/xml/?key='.$key);
        $this->entityManager->persist($entityXml);
        $this->entityManager->flush();
        
        $response = new AlertMessageCollection();
        $response->addAlert('Success', 'Xml template was be saved', AlertMessageCollection::ALERT_TYPE_SUCCESS);
        
        return $response;
    }
    
    public function getXmlPageByKey($key)
    {
        $repository = $this->entityManager->getRepository(TestXml::class);
        $xmlEntity  = $repository->findOneBy(['hash' => $key]);
        
        
        if ($xmlEntity === null) {
            throw new ExpectedException('Incorrect or expired key');
        }
        
        return $xmlEntity->getXmlData();
    }
    
    /**
     * @param $xmlstr
     *
     * @return string
     */
    private function generateHashKey($xmlstr)
    {
        return md5(time().substr($xmlstr, 0, 10));
    }
}