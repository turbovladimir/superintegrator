<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Postbacktable
 *
 * @ORM\Table(name="archive_rows")
 * @ORM\Entity
 */
class ArchiveRows
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
     * @ORM\Column(name="rows", type="text")
     */
    private $rows;

    /**
     * @var int|null
     *
     * @ORM\Column(name="sended", type="integer", nullable=true)
     */
    private $sended = 0;

    public function setRows($rows)
    {
        $this->rows = $rows;
    }
    
    public function getRows()
    {
        return $this->rows;
    }

}
