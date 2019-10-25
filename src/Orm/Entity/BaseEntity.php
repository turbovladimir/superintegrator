<?php
/**
 * Created by PhpStorm.
 * User: v.sadovnikov
 * Date: 18.10.2019
 * Time: 16:28
 */

namespace App\Orm\Entity;

use Doctrine\ORM\Mapping as ORM;

class BaseEntity implements EntityInterface
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;
    
    /**
     * @var string
     *
     * @ORM\Column(name="added_at", type="datetime")
     */
    protected $addedAt;
    
    /**
     * BaseEntity constructor.
     *
     * @throws \Exception
     */
    public function __construct()
    {
        $this->addedAt = new \DateTime('now');
    }
    
    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
    
}