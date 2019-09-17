<?php

namespace App\Entity\Fonbet;

use Doctrine\ORM\Mapping as ORM;

/**
 * Postbacktable
 *
 * @ORM\Table(name="fonbet_statistic_by_publishers")
 * @ORM\Entity
 */
class PublishersStatistic
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
     * @ORM\Column(name="wm_id", type="integer")
     */
    private $wmId;

    /**
     * @var string
     *
     * @ORM\Column(name="click_id", type="text")
     */
    private $clickId;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="registrations", type="integer")
     */
    private $registrations;
    
    /**
     * @var string
     *
     * @ORM\Column(name="deposits_amount", type="text")
     */
    //todo подумать над типом переменной
    private $depositsAmount;
    
    /**
     * @return string
     */
    public function getClickId() : string
    {
        return $this->clickId;
    }
    
    /**
     * @param string $clickId
     */
    public function setClickId(string $clickId) : void
    {
        $this->clickId = $clickId;
    }
    
    /**
     * @return string
     */
    public function getDepositsAmount() : string
    {
        return $this->depositsAmount;
    }
    
    /**
     * @param string $depositsAmount
     */
    public function setDepositsAmount(string $depositsAmount) : void
    {
        $this->depositsAmount = $depositsAmount;
    }
    
    /**
     * @return int
     */
    public function getId() : int
    {
        return $this->id;
    }
    
    /**
     * @param int $id
     */
    public function setId(int $id) : void
    {
        $this->id = $id;
    }
    
    /**
     * @return int
     */
    public function getRegistrations() : int
    {
        return $this->registrations;
    }
    
    /**
     * @param int $registrations
     */
    public function setRegistrations(int $registrations) : void
    {
        $this->registrations = $registrations;
    }
    
    /**
     * @return int
     */
    public function getWmId() : int
    {
        return $this->wmId;
    }
    
    /**
     * @param int $wmId
     */
    public function setWmId(int $wmId) : void
    {
        $this->wmId = $wmId;
    }
}
