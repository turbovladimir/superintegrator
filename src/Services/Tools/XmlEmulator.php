<?php
/**
 * Created by PhpStorm.
 * User: v.sadovnikov
 * Date: 21.08.2019
 * Time: 19:24
 */

namespace App\Services\Tools;

use App\Controller\ToolController;
use App\Repository\TestXmlRepository;
use App\Response\ResponseMessage;
use App\Entity\Superintegrator\TestXml;
use App\Response\ResponsePageParameters;
use App\Response\ResponseXmlPage;
use App\Utils\Serializer;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

class XmlEmulator implements Tool
{
    private $appDomain;
    private $testXmlRepository;
    
    public function __construct(TestXmlRepository $testXmlRepository , string $appDomain)
    {
        $this->appDomain = $appDomain;
        $this->testXmlRepository = $testXmlRepository;
    }

    public function getToolInfo(): array {
        return [
            'title' => 'Xml emulator',
            'description' => 'При помощи данного инструмента вы можете создавать ссылки эмулирующие работу API с форматом ответа xml'
        ];
    }

    public function process(array $parameters, $action = null) {
        if ($action === ToolController::ACTION_DELETE) {
            $this->delete($parameters['id']);
            $response = new ResponseMessage();
            $response->addInfo('Success', 'Xml template was be deleted');
        } elseif ($action === ToolController::ACTION_SAVE) {
            $this->create($parameters['name'], $parameters['xml']);
            $response = new ResponseMessage();
            $response->addInfo('Success', 'Xml template was be saved');
        } elseif ($action === ToolController::ACTION_GET_XML_PAGE) {
            $response = new ResponseXmlPage($this->testXmlRepository->getXmlBodyByKey($parameters['key']));
        } elseif ($action === ToolController::ACTION_GET_LIST){
            $response = new ResponsePageParameters($this->getTableWithXmlTemplates());
        }

        return $response;
    }
    
    /**
     * @param $id
     *
     * @throws
     */
    private function delete($id)
    {
        /**
         * @var $xml TestXml
         */
        $xml = $this->testXmlRepository->find($id);
        
        if (!$xml) {
            throw new BadRequestHttpException('Xml not found');
        }
        
        $this->testXmlRepository->delete($xml);
    }
    
    /**
     *
     * @throws
     */
    private function create($name, $xml)
    {
        libxml_use_internal_errors(true);
        $doc = simplexml_load_string($xml);
        
        if (!$doc) {
            throw new BadRequestHttpException('Invalid xml. Errors: '.implode("\n", libxml_get_errors()));
        }
        
        $entityXml = new TestXml();
        $key       = $this->generateHashKey($xml);
        $entityXml->setName($name);
        $entityXml->setXml($xml);
        $entityXml->setHash($key);
        $this->testXmlRepository->save($entityXml);
    }
    
    /**
     * @return array
     */
    private function getTableWithXmlTemplates()
    {
        
        $xmls = $this->testXmlRepository->findAll();

        if (empty($xmls)) {
            return [];
        }

        foreach ($xmls as $testXml) {
            $url = "http://{$this->appDomain}/xml?key={$testXml->getHash()}";

            $table[] = [
                'id' => $testXml->getId(),
                'name' => $testXml->getName(),
                'added_at' => $testXml->getAddedAt(),
                'url' => "<a target='_blank' href='{$url}'>Go on xml format page</a>",
            ];
        }

        return ['table_head' => array_keys(reset($table)), 'xml_collection' => $table];
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