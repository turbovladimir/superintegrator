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
     * @return string
     */
    public function getXml()
    {
        return $this->xml;
    }
    
    /**
     * @return string
     */
    public function getUrl() : string
    {
        return $this->url;
    }
    
    /**
     * @return string
     */
    public function getAddedAt() : string
    {
        return $this->addedAt->format('Y-m-d H:i:s');
    }
    
    /**
     * @param $xml
     */
    public function setXml($xml)
    {
        $this->xml = $xml;
    }
    
    /**
     * @param $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

}
