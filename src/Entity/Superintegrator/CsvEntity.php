<?php
/**
 * Created by PhpStorm.
 * User: v.sadovnikov
 * Date: 02.09.2019
 * Time: 19:56
 */

namespace App\Entity\Superintegrator;

use Doctrine\ORM\Mapping as ORM;

/**
 * CsvEntity
 *
 * @ORM\Table(name="csv")
 * @ORM\Entity
 */
class CsvEntity
{
    
    /**
     * @var int
     *
     * @ORM\Column(name="file_name", type="string", ength=40 , nullable=false)
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