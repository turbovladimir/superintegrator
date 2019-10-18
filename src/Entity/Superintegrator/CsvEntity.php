<?php
/**
 * Created by PhpStorm.
 * User: v.sadovnikov
 * Date: 02.09.2019
 * Time: 19:56
 */

namespace App\Entity\Superintegrator;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\BaseEntity;
/**
 * CsvEntity
 *
 * @ORM\Table(name="csv")
 * @ORM\Entity
 */
class CsvEntity extends BaseEntity
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
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * @var string
     *
     * @ORM\Column(name="file_name", type="string" , nullable=false)
     */
    private $filename;
    
    public function getFilename()
    {
        return $this->filename;
    }
    
    public function setFilename($filename)
    {
        $this->filename = $filename;
        
        return $this;
    }
}