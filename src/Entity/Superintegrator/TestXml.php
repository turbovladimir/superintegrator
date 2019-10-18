<?php

namespace App\Entity\Superintegrator;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\EntityInterface;
/**
 * TestXml
 *
 * @ORM\Table(name="test_xml", uniqueConstraints={@ORM\UniqueConstraint(name="id", columns={"id"})})
 * @ORM\Entity
 */
class TestXml implements EntityInterface
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string|null
     *
     * @ORM\Column(name="data", type="text", length=65535, nullable=true)
     */
    private $data;
    

    /**
     * @var string
     *
     * @ORM\Column(name="hash", type="string", length=40, nullable=false)
     */
    private $hash;
    
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
    public function setXmlData($xml)
    {
        $this->data = $xml;
    }
    
    
    /**
     * @return string|null
     */
    public function getXmlData()
    {
        return $this->data;
    }
    
    /**
     * @param $hash
     */
    public function setHashCode($hash)
    {
        $this->hash = $hash;
    }

}
