<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Repository\EventTypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiFilter(SearchFilter::class, properties: [
    'host.email' => 'exact',
    'slug'       => 'exact',
])]
#[ApiResource(
    operations: [
        new getCollection(),
        new Get(),
        new Post(
            security: "is_granted('IS_AUTHENTICATED_FULLY')",
        ),
        new Patch(
            security: "is_granted('IS_AUTHENTICATED_FULLY')",
        ),
        new Delete(
            security: "is_granted('IS_AUTHENTICATED_FULLY')",
        ),
    ],
    normalizationContext: [
        'groups' => [
            'event_type:read',
        ],
    ],
    denormalizationContext: [
        'groups' => [
            'event_type:write',
        ],
    ],
)]
#[ORM\Entity(repositoryClass: EventTypeRepository::class)]
class EventType
{
    #[Groups(['event_type:read', 'event:read'])]
    #[ORM\Column]
    #[ORM\GeneratedValue]
    #[ORM\Id]
    private int $id;

    #[Assert\NotBlank]
    #[Groups(['event_type:read', 'event_type:write', 'event:read'])]
    #[ORM\Column(length: 255)]
    private string $name;

    #[Groups(['event_type:read', 'event_type:write', 'event:read'])]
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[Assert\NotBlank]
    #[Assert\Range(min: 5)]
    #[Groups(['event_type:read', 'event_type:write', 'event:read'])]
    #[ORM\Column]
    private int $duration;

    #[Assert\NotBlank]
    #[Assert\Regex(
        pattern: '/^[a-z0-9-]+$/',
        message: 'The slug can only contain lowercase letters, numbers and dashes.',
    )]
    #[Groups(['event_type:read', 'event_type:write', 'event:read'])]
    #[ORM\Column(length: 255)]
    private string $slug;

    #[Groups(['event_type:read'])]
    #[ORM\JoinColumn(nullable: false)]
    #[ORM\ManyToOne(inversedBy: 'eventTypes')]
    private User $host;

    /** @var Collection<int, Event> */
        #[ORM\OneToMany(targetEntity: Event::class, mappedBy: 'eventType')]
    private Collection $events;

    public function __construct()
    {
        $this->events = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getDuration(): int
    {
        return $this->duration;
    }

    public function setDuration(int $duration): static
    {
        $this->duration = $duration;

        return $this;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

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

    /** @return Collection<int, Event> */
    public function getEvents(): Collection
    {
        return $this->events;
    }

    public function addEvent(Event $event): static
    {
        if (!$this->events->contains($event)) {
            $this->events->add($event);
            $event->setEventType($this);
        }

        return $this;
    }

    public function removeEvent(Event $event): static
    {
        if ($this->events->removeElement($event)) {
            // set the owning side to null (unless already changed)
            if ($event->getEventType() === $this) {
                $event->setEventType(null);
            }
        }

        return $this;
    }
}
