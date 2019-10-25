<?php
/**
 * Created by PhpStorm.
 * User: v.sadovnikov
 * Date: 02.09.2019
 * Time: 19:56
 */

namespace App\Orm\Entity\Superintegrator;

use Doctrine\ORM\Mapping as ORM;
use App\Orm\Entity\EntityInterface;
/**
 * CsvEntity
 *
 * @ORM\Table(name="csv")
 * @ORM\Entity
 */
class CsvEntity implements EntityInterface
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