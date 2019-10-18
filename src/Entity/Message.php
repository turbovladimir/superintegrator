<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Message
 *
 * @ORM\Table(name="messages")
 * @ORM\Entity
 */
class Message implements EntityInterface
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
     * @ORM\Column(name="message", type="text")
     */
    private $message;
    
    /**
     * @var int|null
     *
     * @ORM\Column(name="sended", type="integer", nullable=true)
     */
    private $sended = 0;
    
    /**
     * @var integer|null
     *
     * @ORM\Column(name="attempts", type="integer")
     */
    private $attempts;
    
    /**
     * @var string|null
     *
     * @ORM\Column(name="error_text", type="text", nullable=true)
     */
    private $errorText;
    
    /**
     * @var string
     *
     * @ORM\Column(name="added_at", type="datetime")
     */
    private $addedAt;
    
    
    /**
     * @var string
     *
     * @ORM\Column(name="sended_at", type="datetime", nullable=true)
     */
    private $sendedAt;
    
    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    public function setMessage($message)
    {
        $this->message = $message;
        $this->addedAt = date('Y-m-d H:i:s');
    }
    
    public function getMessage()
    {
        return $this->message;
    }
    
    /**
     * @return string|null
     */
    public function getErrorText() : ?string
    {
        return $this->errorText;
    }
    
    /**
     * @param string|null $errorText
     */
    public function setErrorText(?string $errorText) : void
    {
        $this->errorText = $errorText;
    }
    
    public function setSended()
    {
        $this->sended = 1;
        $this->sendedAt = date('Y-m-d H:i:s');
    }
    
    /**
     * @param string|null $attempts
     */
    public function setAttempts(?string $attempts) : void
    {
        $this->attempts = $attempts;
    }
    
    /**
     * @return integer|null
     */
    public function getAttempts()
    {
        return $this->attempts;
    }
}
