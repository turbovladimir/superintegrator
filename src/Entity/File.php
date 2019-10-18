<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * File
 *
 * @ORM\Table(name="files")
 * @ORM\Entity
 */
class File implements EntityInterface
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
     * @ORM\Column(name="file_name", type="text")
     */
    private $fileName;

    /**
     * @var string|null
     *
     * @ORM\Column(name="file_content", type="blob")
     */
    private $fileContent;
    
    /**
     * @var string
     *
     * @ORM\Column(name="added_at", type="datetime")
     */
    private $addedAt;
    
    
    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     */
    public function setAddedAt()
    {
        $this->addedAt = new \DateTime('now');
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
     * @return string|null
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
