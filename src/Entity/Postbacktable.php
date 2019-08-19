<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Postbacktable
 *
 * @ORM\Table(name="postbacktable")
 * @ORM\Entity
 */
class Postbacktable
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
     * @ORM\Column(name="url", type="string", length=10000, nullable=true)
     */
    private $url;

    /**
     * @var int|null
     *
     * @ORM\Column(name="sended", type="integer", nullable=true)
     */
    private $sended = '0';

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getSended(): ?int
    {
        return $this->sended;
    }

    public function setSended(?int $sended): self
    {
        $this->sended = $sended;

        return $this;
    }


}
