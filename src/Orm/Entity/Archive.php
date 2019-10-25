<?php
/**
 * Created by PhpStorm.
 * User: v.sadovnikov
 * Date: 25.10.2019
 * Time: 17:43
 */

namespace App\Orm\Entity;

use Doctrine\ORM\Mapping as ORM;
/**
 * Хранит в себе различные логи не привязываясь к какому то конкретному сервису
 *
 * Class Archive
 *
 * @ORM\Table(name="archives")
 * @ORM\Entity
 */
class Archive extends BaseEntity
{
    /**
     * Имя источника
     *
     * @var string|null
     *
     * @ORM\Column(name="source", type="text")
     */
    private $source;
    
    /**
     * Содержимое лога
     *
     * @var string|null
     *
     * @ORM\Column(name="log_data", type="text")
     */
    private $logData;
    
    /**
     * @param string|null $logData
     */
    public function setLogData(?string $logData) : void
    {
        $this->logData = $logData;
    }
    
    /**
     * @param string|null $source
     */
    public function setSource(?string $source) : void
    {
        $this->source = $source;
    }
    
    /**
     * @return string|null
     */
    public function getLogData() : ?string
    {
        return $this->logData;
    }
    
    /**
     * @return string|null
     */
    public function getSource() : ?string
    {
        return $this->source;
    }
}