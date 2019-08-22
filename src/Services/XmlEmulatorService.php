<?php
/**
 * Created by PhpStorm.
 * User: v.sadovnikov
 * Date: 21.08.2019
 * Time: 19:24
 */

namespace App\Services;

use App\Entity\TestXml;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;

class XmlEmulatorService
{
    public static function sendResponse(EntityManagerInterface $entityManager, $parameters)
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
        } else {
            $entityXml = new TestXml();
            $key = self::generateHashKey($xmlstr);
            $entityXml->setXmlData($xmlstr);
            $entityXml->setHashCode($key);
            $entityManager->persist($entityXml);
            $entityManager->flush();
            
            $response = new Response(json_encode(['url' => 'http://'.$_SERVER["HTTP_HOST"].'/xml/?key='.$key]));
            $response->send();
        }
    }
    
    public static function getXmlPageByKey(EntityManagerInterface $entityManager, $key)
    {
        $repository = $entityManager->getRepository(TestXml::class);
        $xmlEntity = $repository->findOneBy(['hash' => $key]);
        $xml = $xmlEntity->getXmlData();
        $response = new Response($xml, 200, ['Content-Type' => 'text/xml']);
        $response->send();
        
    }
    
    /**
     * @param $xmlstr
     *
     * @return string
     */
    private static function generateHashKey($xmlstr)
    {
        return md5(time().substr($xmlstr, 0, 10));
    }
}