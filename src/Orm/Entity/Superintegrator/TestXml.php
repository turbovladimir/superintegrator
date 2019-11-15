<?php

namespace App\Orm\Entity\Superintegrator;

use Doctrine\ORM\Mapping as ORM;
use App\Orm\Entity\BaseEntity;
/**
 * TestXml
 *
 * @ORM\Table(name="test_xml", uniqueConstraints={@ORM\UniqueConstraint(name="id", columns={"id"})})
 * @ORM\Entity
 */
class TestXml extends BaseEntity
{

    /**
     * @var string|null
     *
     * @ORM\Column(name="xml", type="text", length=65535, nullable=true)
     */
    private $xml;
    

    /**
     * @var string
     *
     * @ORM\Column(name="url", type="string", length=80, nullable=false)
     */
    private $url;
    
    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * @param $xml
     */
    public function setXml($xml)
    {
        $this->xml = $xml;
    }
    
    
    /**
     * @return string|null
     */
    public function getXml()
    {
        return $this->xml;
    }
    
    /**
     * @param $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

}
