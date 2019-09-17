<?php
/**
 * Created by PhpStorm.
 * User: v.sadovnikov
 * Date: 21.08.2019
 * Time: 19:24
 */

namespace App\Services;

use App\Entity\Superintegrator\TestXml;
use App\Exceptions\ExpectedException;

class XmlEmulatorService extends AbstractService
{
    /**
     * @param $parameters
     *
     * @return false|string
     * @throws \Exception
     */
    public function process($parameters)
    {
        if (empty($parameters['xml'])) {
            exit();
        }
    
        $xmlstr = $parameters['xml'];
        libxml_use_internal_errors(true);
        $doc = simplexml_load_string($xmlstr);
        $xml = explode("\n", $xmlstr);
    
        if (!$doc) {
            $errors = libxml_get_errors();
        
            foreach ($errors as $error) {
                echo display_xml_error($error, $xml);
            }
        
            libxml_clear_errors();
            
            throw new ExpectedException('Invalid xml');
        } else {
            $entityXml = new TestXml();
            $key = $this->generateHashKey($xmlstr);
            $entityXml->setXmlData($xmlstr);
            $entityXml->setHashCode($key);
            $this->entityManager->persist($entityXml);
            $this->entityManager->flush();
            
            return json_encode(['url' => 'http://'.$_SERVER["HTTP_HOST"].'/xml/?key='.$key]);
        }
    }
    
    public function getXmlPageByKey($key)
    {
        $repository = $this->entityManager->getRepository(TestXml::class);
        $xmlEntity = $repository->findOneBy(['hash' => $key]);
        
        
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