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
use App\Utils\Serializer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

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
    
    public function getCollection()
    {
        $repository = $this->entityManager->getRepository(TestXml::class);
        $xmlCollections = $repository->findAll();
        return json_decode(Serializer::get()->serialize($xmlCollections, 'json', [AbstractNormalizer::IGNORED_ATTRIBUTES => ['xml']]), true);
    }
    
    /**
     * @param Request $request
     *
     * @return Response
     * @throws ExpectedException
     */
    public function getXmlPage(Request $request)
    {
        parse_str($request->getQueryString(), $parameters);
        
        if (empty($parameters['key'])) {
            throw new ExpectedException('Cannot parse key from url');
        }
        

        $query = $this->entityManager->createQuery('SELECT t FROM ' . TestXml::class . ' t WHERE t.url LIKE :word');
        $query->setParameter('word', "%{$parameters['key']}%")->setMaxResults(1);
        $xmlEntity  = $query->getResult();
        
        if ($xmlEntity === null) {
            throw new ExpectedException('Incorrect or expired key');
        }

        
        return new Response(reset($xmlEntity)->getXml(), 200, ['Content-Type' => 'text/xml']);
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