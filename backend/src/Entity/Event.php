<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Repository\EventRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    operations: [
        new getCollection(),
        new Get(),
        new Post(),
        new Patch(),
        new Delete(),
    ],
    normalizationContext: [
        'groups' => [
            'event:read',
        ],
    ],
    denormalizationContext: [
        'groups' => [
            'event:write',
        ],
    ],
)]
#[ORM\Entity(repositoryClass: EventRepository::class)]
class Event
{
    #[Groups(['event:read'])]
    #[ORM\Column]
    #[ORM\GeneratedValue]
    #[ORM\Id]
    private int $id;

    #[Assert\NotBlank]
    #[Assert\Type(
        type: 'App\Entity\EventType',
    )]
    #[Groups(['event:read', 'event:write'])]
    #[ORM\JoinColumn(nullable: false)]
    #[ORM\ManyToOne]
    private EventType $type;

    #[Assert\NotBlank]
    #[Assert\Type(
        type: 'DateTime',
        message: 'The end date must be after the start date.',
    )]
    #[Groups(['event:read', 'event:write'])]
    #[ORM\Column]
    private \DateTime $startDateTime;

    #[Assert\NotBlank]
    #[Assert\Type(
        type: 'DateTime',
        message: 'The end date must be after the start date.',
    )]
    #[Groups(['event:read', 'event:write'])]
    #[ORM\Column]
    private \DateTime $endDateTime;

    #[Assert\NotBlank]
    #[Groups(['event:read', 'event:write'])]
    #[ORM\Column(length: 255)]
    private string $participantName;

    #[Assert\Email]
    #[Assert\NotBlank]
    #[Groups(['event:read', 'event:write'])]
    #[ORM\Column(length: 255)]
    private string $participantEmail;

    #[Groups(['event:read', 'event:write'])]
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

    public function setParticipantMessage(?string $participantMessage): static
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
