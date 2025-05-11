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
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    operations: [
        new getCollection(
            security: "is_granted('IS_AUTHENTICATED_FULLY')",
        ),
        new Get(),
        new Post(),
        new Patch(),
        new Delete(
            security: "is_granted('IS_AUTHENTICATED_FULLY')",
        ),
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
    #[ORM\ManyToOne(targetEntity: EventType::class, cascade: ['persist'], inversedBy: 'events')]
    private EventType $eventType;

    #[Assert\NotBlank]
    #[Groups(['event:read', 'event:write'])]
    #[ORM\Column(type: Types::TIME_MUTABLE, nullable: false)]
    private \DateTime $startTime;

    #[Assert\NotBlank]
    #[Groups(['event:read', 'event:write'])]
    #[ORM\Column(type: Types::TIME_MUTABLE, nullable: false)]
    private \DateTime $endTime;

    #[Groups(['event:read', 'event:write'])]
    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private \DateTime $day;

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

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEventType(): EventType
    {
        return $this->eventType;
    }

    public function setEventType(?EventType $eventType): static
    {
        if ($eventType instanceof EventType) {
            $this->eventType = $eventType;
        }

        return $this;
    }

    public function getStartTime(): \DateTime
    {
        return $this->startTime;
    }

    public function setStartTime(\DateTime $startTime): static
    {
        $this->startTime = $startTime;

        return $this;
    }

    public function getEndTime(): \DateTime
    {
        return $this->endTime;
    }

    public function setEndTime(\DateTime $endTime): static
    {
        $this->endTime = $endTime;

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

    public function getDay(): \DateTime
    {
        return $this->day;
    }

    public function setDay(\DateTime $day): static
    {
        $this->day = $day;

        return $this;
    }
}
