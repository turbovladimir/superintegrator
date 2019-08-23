<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CityadsCountryRussia
 *
 * @ORM\Table(name="cityads_country_russia", uniqueConstraints={@ORM\UniqueConstraint(name="id", columns={"id"})})
 * @ORM\Entity
 */
class CityadsCountryRussia
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
