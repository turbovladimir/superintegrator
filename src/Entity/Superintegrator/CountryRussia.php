<?php

namespace App\Entity\Superintegrator;

use App\Entity\BaseEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * CountryRussia
 *
 * @ORM\Table(name="cityads_country_russia", uniqueConstraints={@ORM\UniqueConstraint(name="id", columns={"id"})})
 * @ORM\Entity
 */
class CountryRussia extends BaseEntity
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
     * @ORM\Column(name="name", type="string", length=40, nullable=false)
     */
    private $name;

    /**
     * @var int
     *
     * @ORM\Column(name="cityads_id", type="integer", nullable=false)
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
