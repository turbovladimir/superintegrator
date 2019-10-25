<?php

namespace App\Orm\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Сущность с контентом для страниц html
 *
 * @ORM\Table(name="pages")
 * @ORM\Entity
 */
class Page extends BaseEntity
{
    /**
     * @var string
     * @ORM\Column(name="name", type="text")
     */
    private $name;
    
    /**
     * @var string
     * @ORM\Column(name="content_type", type="text")
     */
    private $contentType;
    
    /**
     * @var string
     * @ORM\Column(name="content", type="text")
     */
    private $content;
    
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
        $this->name = $name;
    }
    
    /**
     * @return string
     */
    public function getContentType() : string
    {
        return $this->contentType;
    }
    
    /**
     * @param string $contentType
     */
    public function setContentType(string $contentType) : void
    {
        $this->contentType = $contentType;
    }
    
    /**
     * @return string
     */
    public function getContent() : string
    {
        return $this->content;
    }
    
    /**
     * @param string $content
     */
    public function setContent(string $content) : void
    {
        $this->content = $content;
    }
}
