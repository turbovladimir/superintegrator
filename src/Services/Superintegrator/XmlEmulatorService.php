<?php
/**
 * Created by PhpStorm.
 * User: v.sadovnikov
 * Date: 21.08.2019
 * Time: 19:24
 */

namespace App\Services\Superintegrator;

use App\Controller\ToolController;
use App\Repository\TestXmlRepository;
use App\Response\AlertMessage;
use App\Entity\Superintegrator\TestXml;
use App\Exceptions\ExpectedException;
use App\Utils\Serializer;
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
     * @return AlertMessage
     * @throws ExpectedException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function delete($id)
    {
        $xml = $this->testXmlRepository->findOneBy(['id' => $id]);
        
        if (!$xml) {
            throw new ExpectedException('Xml not found');
        }
        
        $this->testXmlRepository->getEntityManager()->remove($xml);
        $this->testXmlRepository->getEntityManager()->flush();
        $response = new AlertMessage();
        $response->addAlert('Success', 'Xml template was be deleted');
    
        return $response;
    }
    
    /**
     * @param Request $request
     *
     * @return AlertMessage
     * @throws ExpectedException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function create(Request $request)
    {
        $name = $request->get('name');
        $xml = $request->get('xml');
        libxml_use_internal_errors(true);
        $doc = simplexml_load_string($xml);
        
        if (!$doc) {
            throw new ExpectedException('Invalid xml. Errors: '.implode("\n", libxml_get_errors()));
        }
        
        $entityXml = new TestXml();
        $key       = $this->generateHashKey($xml);
        $entityXml->setName($name);
        $entityXml->setXml($xml);
        $entityXml->setUrl('http://'.$this->appDomain. '/tools/' . ToolController::XML_EMULATOR . '/' . ToolController::ACTION_GET_XML_PAGE . '/?key='.$key);
        $this->testXmlRepository->getEntityManager()->persist($entityXml);
        $this->testXmlRepository->getEntityManager()->flush();
        
        $response = new AlertMessage();
        $response->addAlert('Success', 'Xml template was be saved');
        
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