<?php

namespace App\Entity\Superintegrator;

use App\Entity\EntityInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * WorldRegion
 *
 * @ORM\Table(name="cityads_world_region", uniqueConstraints={@ORM\UniqueConstraint(name="cityads_world_region_id_uindex", columns={"id"})})
 * @ORM\Entity
 */
class WorldRegion implements EntityInterface
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
    
    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
    
            /**
     * @return string
     */
    public function getCityadsId()
    {
        return $this->cityadsId;
    }


}
