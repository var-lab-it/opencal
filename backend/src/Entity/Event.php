<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\EventRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EventRepository::class)]
class Event
{
    #[ORM\Column]
    #[ORM\GeneratedValue]
    #[ORM\Id]
    private int $id;

    #[ORM\JoinColumn(nullable: false)]
    #[ORM\ManyToOne]
    private EventType $type;

    #[ORM\Column]
    private \DateTime $startDateTime;

    #[ORM\Column]
    private \DateTime $endDateTime;

    #[ORM\Column(length: 255)]
    private string $participantName;

    #[ORM\Column(length: 255)]
    private string $participantEmail;

    #[ORM\Column(type: 'text', length: 255, nullable: true)]
    private ?string $participantMessage = null;

    #[ORM\JoinColumn(nullable: false)]
    #[ORM\ManyToOne(inversedBy: 'events')]
    private User $host;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): EventType
    {
        return $this->type;
    }

    public function setType(EventType $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getStartDateTime(): \DateTime
    {
        return $this->startDateTime;
    }

    public function setStartDateTime(\DateTime $startDateTime): static
    {
        $this->startDateTime = $startDateTime;

        return $this;
    }

    public function getEndDateTime(): \DateTime
    {
        return $this->endDateTime;
    }

    public function setEndDateTime(\DateTime $endDateTime): static
    {
        $this->endDateTime = $endDateTime;

        return $this;
    }

    public function getParticipantName(): string
    {
        return $this->participantName;
    }

    public function setParticipantName(string $participantName): static
    {
        $this->participantName = $participantName;

        return $this;
    }

    public function getParticipantEmail(): string
    {
        return $this->participantEmail;
    }

    public function setParticipantEmail(string $participantEmail): static
    {
        $this->participantEmail = $participantEmail;

        return $this;
    }

    public function getParticipantMessage(): ?string
    {
        return $this->participantMessage;
    }

    public function setParticipantMessage(string $participantMessage): static
    {
        $this->participantMessage = $participantMessage;

        return $this;
    }

    public function getHost(): User
    {
        return $this->host;
    }

    public function setHost(?User $host): static
    {
        if ($host instanceof User) {
            $this->host = $host;
        }

        return $this;
    }
}
