<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity(fields={"email"}, message="У вас уже есть аккаунт")
 */
class User extends BaseEntity implements UserInterface
{
    public const ROLE_USER = 'user';
    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $password;
    
    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $confirmationCode;
    
    /**
     * @var bool
     *
     * @ORM\Column(type="boolean")
     */
    private $enabled;
    
    /**
     * User constructor.
     *
     * @throws \Exception
     */
    public function __construct()
    {
        $this->roles = [self::ROLE_USER];
        $this->enabled = false;
        
        parent::__construct();
    }
    
    /**
     * @return string
     */
    public function getConfirmationCode(): string
    {
        return $this->confirmationCode;
    }
    
    /**
     * @param string $confirmationCode
     *
     * @return User
     */
    public function setConfirmationCode(string $confirmationCode): self
    {
        $this->confirmationCode = $confirmationCode;
        
        return $this;
    }
    
    /**
     * @return bool
     */
    public function getEnabled(): bool
    {
        return $this->enabled;
    }
    
    /**
     * @param bool $enabled
     *
     * @return User
     */
    public function setEnable(bool $enabled): self
    {
        $this->enabled = $enabled;
        
        return $this;
    }

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $plainPassword;

    /**
     * @ORM\Column(type="json_array")
     */
    private $roles = [];
    
    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }
    
    /**
     * @param string $email
     *
     * @return $this
     */
    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    public function getRoles(): ?array
    {
        return $this->roles;
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->name;
    }

    public function setUserName(string $name): self
    {
        $this->name = $name;

        return $this;
    }
    
    public function getSalt()
    {
        // TODO: Implement getSalt() method.
    }
    
    public function eraseCredentials()
    {
        $this->plainPassword = null;
    }
}
