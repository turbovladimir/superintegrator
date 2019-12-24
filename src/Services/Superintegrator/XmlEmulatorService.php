<?php
/**
 * Created by PhpStorm.
 * User: v.sadovnikov
 * Date: 21.08.2019
 * Time: 19:24
 */

namespace App\Services\Superintegrator;

use App\Controller\HttpController;
use App\Repository\TestXmlRepository;
use App\Response\AlertMessageCollection;
use App\Entity\Superintegrator\TestXml;
use App\Exceptions\ExpectedException;
use App\Services\AbstractService;
use App\Utils\Serializer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

class XmlEmulatorService
{
    private $appDomain;
    private $testXmlRepository;
    
    public function __construct(TestXmlRepository $testXmlRepository , string $appDomain)
    {
        $this->appDomain = $appDomain;
        $this->testXmlRepository = $testXmlRepository;
    }
    
    /**
     * @param Request $request
     *
     * @return AlertMessageCollection
     * @throws ExpectedException
     */
    public function processRequest(Request $request)
    {
        parse_str($request->getContent(), $parameters);
        
        if (isset($parameters['new']) && !empty($parameters['name']) && !empty($parameters['xml_str'])) {
            return $this->createApiSource($parameters['name'], $parameters['xml_str']);
        } elseif (!empty($parameters['delete'])) {
            return $this->deleteById($parameters['delete']);
        } else {
            throw new ExpectedException('Incorrect parameters in post');
        }
    }
    
    /**
     * @param Request $request
     *
     * @return mixed
     * @throws ExpectedException
     */
    public function getXml(Request $request)
    {
        parse_str($request->getQueryString(), $parameters);
    
        if (empty($parameters['key'])) {
            throw new ExpectedException('Cannot parse key from url');
        }
        
        return $this->testXmlRepository->getXmlBodyByKey($parameters['key']);
    }
    
    /**
     * @param $id
     *
     * @return AlertMessageCollection
     * @throws ExpectedException
     */
    private function deleteById($id)
    {
        $xml = $this->testXmlRepository->findOneBy(['id' => $id]);
        
        if (!$xml) {
            throw new ExpectedException('Xml not found');
        }
        
        $this->testXmlRepository->getEntityManager()->remove($xml);
        $this->testXmlRepository->getEntityManager()->flush();
        $response = new AlertMessageCollection();
        $response->addAlert('Success', 'Xml template was be deleted', AlertMessageCollection::ALERT_TYPE_SUCCESS);
    
        return $response;
    }
    
    /**
     * @param string $name
     * @param string $xml
     *
     * @return AlertMessageCollection
     * @throws ExpectedException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    private function createApiSource(string $name, string $xml)
    {
        libxml_use_internal_errors(true);
        $doc = simplexml_load_string($xml);
        
        if (!$doc) {
            throw new ExpectedException('Invalid xml. Errors: '.implode("\n", libxml_get_errors()));
        }
        
        $entityXml = new TestXml();
        $key       = $this->generateHashKey($xml);
        $entityXml->setName($name);
        $entityXml->setXml($xml);
        $entityXml->setUrl('http://'.$_SERVER['HTTP_HOST']. '/' . HttpController::PAGE_XML_EMULATOR . '/' . HttpController::ACTION_XML . '/?key='.$key);
        $this->testXmlRepository->getEntityManager()->persist($entityXml);
        $this->testXmlRepository->getEntityManager()->flush();
        
        $response = new AlertMessageCollection();
        $response->addAlert('Success', 'Xml template was be saved', AlertMessageCollection::ALERT_TYPE_SUCCESS);
        
        return $response;
    }
    
    /**
     * @return array
     */
    public function getTableWithXmlTemplates()
    {
        
        $collection = $this->testXmlRepository->findAll();
        $collection = json_decode(Serializer::get()->serialize($collection, 'json', [AbstractNormalizer::IGNORED_ATTRIBUTES => ['xml']]), true);
        
        if (!$collection) {
            return [];
        }
        
        return ['table_head' => array_keys(reset($collection)), 'xml_collection' => $collection];
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