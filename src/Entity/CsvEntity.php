<?php
/**
 * Created by PhpStorm.
 * User: v.sadovnikov
 * Date: 02.09.2019
 * Time: 19:56
 */

namespace App\Entity;

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
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;
    
    /**
     * @var int
     *
     * @ORM\Column(name="file_name", type="string", length=40 , nullable=false)
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