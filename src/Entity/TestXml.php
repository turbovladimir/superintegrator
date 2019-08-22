<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TestXml
 *
 * @ORM\Table(name="test_xml", uniqueConstraints={@ORM\UniqueConstraint(name="id", columns={"id"})})
 * @ORM\Entity
 */
class TestXml
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

//    /**
//     * @var \DateTime
//     *
//     * @ORM\Column(name="creation_date", type="datetime", nullable=false, options={"default"="CURRENT_TIMESTAMP"})
//     */
//    private $creationDate = 'CURRENT_TIMESTAMP';

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
