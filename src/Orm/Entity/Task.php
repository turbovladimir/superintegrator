<?php
/**
 * Created by PhpStorm.
 * User: v.sadovnikov
 * Date: 25.10.2019
 * Time: 11:57
 */

namespace App\Orm\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Task
 *
 * @ORM\Table(name="messages")
 * @ORM\Entity
 */
class Task extends BaseEntity
{
    /**
     * @var string
     * @ORM\Column(name="task", type="text")
     */
    protected $task;
    
    /**
     * @var string
     * @ORM\Column(name="due_date", type="datetime")
     */
    protected $dueDate;
    
    /**
     * @return mixed
     */
    public function getTask()
    {
        return $this->task;
    }
    
    /**
     * @param $task
     */
    public function setTask($task)
    {
        $this->task = $task;
    }
    
    /**
     * @return mixed
     */
    public function getDueDate()
    {
        return $this->dueDate;
    }
    
    /**
     * @param \DateTime|null $dueDate
     */
    public function setDueDate(\DateTime $dueDate = null)
    {
        $this->dueDate = $dueDate;
    }
}