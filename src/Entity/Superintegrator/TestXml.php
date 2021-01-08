<?php

namespace App\Entity\Superintegrator;

use App\Controller\ToolController;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\BaseEntity;

/**
 * TestXml
 *
 * @ORM\Table(name="test_xml", uniqueConstraints={@ORM\UniqueConstraint(name="id", columns={"id"})})
 * @ORM\Entity
 */
class TestXml extends BaseEntity
{
    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=80, nullable=false)
     */
    private $name;

    /**
     * @var string|null
     *
     * @ORM\Column(name="xml", type="text", length=65535, nullable=true)
     */
    private $xml;
    

    /**
     * @var string
     *
     * @ORM\Column(name="hash", type="string", length=80, nullable=false)
     */
    private $hash;
    
    /**
     * @return string
     */
    public function getName() : string
    {
        return $this->name;
    }
    
    /**
     * @param string $name
     */
    public function setName(string $name) : void
    {
        $this->name = trim($name);
    }
    
    /**
     * @return string
     */
    public function getXml()
    {
        return $this->xml;
    }
    
    /**
     * @param $xml
     */
    public function setXml($xml)
    {
        $this->xml = $xml;
    }
    
    /**
     * @param string $hash
     */
    public function setHash(string $hash) : void
    {
        $this->hash = $hash;
    }
    
    /**
     * @return string
     */
    public function getHash() : string
    {
        return $this->hash;
    }
}
