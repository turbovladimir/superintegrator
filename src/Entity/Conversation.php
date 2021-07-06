<?php

namespace App\Entity;

use App\Repository\ConversationRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ConversationRepository::class)
 */
class Conversation
{
    const STATUS_OPENED = 'opened';
    const STATUS_CLOSED = 'closed';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $lastModify;

    /**
     * @ORM\Column(type="integer")
     */
    private $lastUpdateId;

    /**
     * @ORM\Column(type="integer")
     */
    private $userId;

    /**
     * @ORM\Column(type="integer")
     */
    private $chatId;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $command;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $status;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $notes = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLastUpdateId() {
        return $this->lastUpdateId;
    }

    public function setLastUpdateId(int $lastUpdateId): self {
        $this->lastUpdateId = $lastUpdateId;

        return $this;
    }

    public function getLastModify(): ?\DateTimeInterface
    {
        return $this->lastModify;
    }

    public function setLastModify(\DateTimeInterface $lastModify): self
    {
        $this->lastModify = $lastModify;

        return $this;
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): self
    {
        $this->userId = $userId;

        return $this;
    }

    public function getChatId(): ?int
    {
        return $this->chatId;
    }

    public function setChatId(int $chatId): self
    {
        $this->chatId = $chatId;

        return $this;
    }

    public function getCommand(): ?string
    {
        return $this->command;
    }

    public function setCommand(string $command = null): self
    {
        $this->command = $command;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getNotes(): ?array
    {
        return $this->notes;
    }

    public function setNotes(array $notes): self
    {
        $this->notes = $notes;

        return $this;
    }

    public function addNote(string $name, $value)
    {
        $this->notes = array_merge($this->notes, [$name => $value]);
    }

    public function addMessageInHistory(\Longman\TelegramBot\Entities\Message $message)
    {
        $this->notes['messages'][] = $message->getMessageId();
    }

    public function getMessageHistory() : array
    {
        return $this->notes['messages'] ?? [];
    }
}
