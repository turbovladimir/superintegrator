<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Message
 *
 * @ORM\Table(name="messages")
 * @ORM\Entity
 */
class Message extends BaseEntity
{
    /**
     * @var string|null
     *
     * @ORM\Column(name="destination", type="text")
     */
    private $destination;
    
    /**
     * @var string|null
     *
     * @ORM\Column(name="url", type="text")
     */
    private $url;
    
    /**
     * @var string|null
     *
     * @ORM\Column(name="headers", type="text", nullable=true)
     */
    private $headers;
    
    /**
     * @var string|null
     *
     * @ORM\Column(name="method", type="text")
     */
    private $method;
    
    
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
    private $attempts = 0;
    
    /**
     * @var string|null
     *
     * @ORM\Column(name="error_text", type="text", nullable=true)
     */
    private $errorText;
    
    
    /**
     * @var string
     *
     * @ORM\Column(name="sended_at", type="datetime", nullable=true)
     */
    private $sendedAt;
    
    
    /**
     * Message constructor.
     *
     * @param $destination
     * @param $url
     * @param $headers
     * @param $method
     *
     * @throws \Exception
     */
    public function __construct($destination, $url, $headers = null, $method = 'GET')
    {
        $this->destination = $destination;
        $this->url         = $url;
        $this->headers     = $headers;
        $this->method      = $method;
        parent::__construct();
    }
    
    public function getUrl()
    {
        return $this->url;
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
        $this->sended   = 1;
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
