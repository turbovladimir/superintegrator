<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use http\Encoding\Stream;

/**
 * File
 *
 * @ORM\Table(name="files")
 * @ORM\Entity
 */
class File extends BaseEntity
{
    
    /**
     * @var string|null
     *
     * @ORM\Column(name="file_name", type="text")
     */
    private $fileName;
    
    /**
     * @var string|null
     *
     * @ORM\Column(name="type", type="text")
     */
    private $type;

    /**
     * @var Stream|null
     *
     * @ORM\Column(name="file_content", type="blob")
     */
    private $fileContent;
    
    /**
     * @param string|null $type
     */
    public function setType(?string $type) : void
    {
        $this->type = $type;
    }
    
    /**
     * @param $fileContent
     */
    public function setFileContent($fileContent)
    {
        $this->fileContent = $fileContent;
    }
    
    /**
     * @param string|null $fileName
     */
    public function setFileName(?string $fileName) : void
    {
        $this->fileName = $fileName;
    }
    
    /**
     * @return Stream|null
     */
    public function getFileContent()
    {
        return $this->fileContent;
    }
    
    /**
     * @return string|null
     */
    public function getFileName() : ?string
    {
        return $this->fileName;
    }
}
