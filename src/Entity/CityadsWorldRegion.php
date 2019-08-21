<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CityadsWorldRegion
 *
 * @ORM\Table(name="cityads_world_region", uniqueConstraints={@ORM\UniqueConstraint(name="cityads_world_region_id_uindex", columns={"id"})})
 * @ORM\Entity
 */
class CityadsWorldRegion
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
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=80, nullable=false)
     */
    private $name;

    /**
     * @var int|null
     *
     * @ORM\Column(name="cityads_id", type="integer", nullable=true)
     */
    private $cityadsId;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getCityadsId(): ?int
    {
        return $this->cityadsId;
    }

    public function setCityadsId(?int $cityadsId): self
    {
        $this->cityadsId = $cityadsId;

        return $this;
    }


}