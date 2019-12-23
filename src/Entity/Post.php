<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PostRepository")
 */
class Post extends BaseEntity
{
    /**
     * @ORM\Column(type="string", nullable=false)
     */
    private $title;
    
    /**
     * @ORM\Column(type="text", nullable=false)
     */
    private $body;
    
    /**
     * @ORM\Column(type="string", nullable=false)
     */
    private $slug;
    
    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }
    
    /**
     * @param mixed $title
     */
    public function setTitle($title) : void
    {
        $this->title = $title;
    }
    
    /**
     * @return mixed
     */
    public function getSlug()
    {
        return $this->slug;
    }
    
    /**
     * @param mixed $slug
     */
    public function setSlug($slug) : void
    {
        $this->slug = $slug;
    }
    
    /**
     * @return string
     */
    public function getAddedAt() : string
    {
        return $this->addedAt;
    }
    
    /**
     * @param string $addedAt
     */
    public function setAddedAt(string $addedAt) : void
    {
        $this->addedAt = $addedAt;
    }
    
    /**
     * @return mixed
     */
    public function getBody()
    {
        return $this->body;
    }
    
    /**
     * @param mixed $body
     */
    public function setBody($body) : void
    {
        $this->body = $body;
    }
}
