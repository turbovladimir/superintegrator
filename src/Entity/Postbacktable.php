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


}
