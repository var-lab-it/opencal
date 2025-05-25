<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Patch;
use App\Repository\UserRepository;
use App\State\CurrentUserProvider;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Safe\DateTimeImmutable;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    operations: [
        new Get(
            uriTemplate: '/me',
            security: "is_granted('IS_AUTHENTICATED_FULLY')",
            provider: CurrentUserProvider::class,
        ),
        new Patch(
            uriTemplate: '/me/{id}',
            security: "is_granted('IS_AUTHENTICATED_FULLY')",
        ),
    ],
    normalizationContext: [
        'groups' => [
            'me:read',
        ],
    ],
    denormalizationContext: [
        'groups' => [
            'me:write',
        ],
    ],
)]
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    public const string ROLE_USER  = 'ROLE_USER';
    public const string ROLE_ADMIN = 'ROLE_ADMIN';

    #[Groups(['me:read', 'me:write'])]
    #[ORM\Column]
    #[ORM\GeneratedValue]
    #[ORM\Id]
    private ?int $id = null;

    #[Assert\Email]
    #[Assert\NotBlank]
    #[Groups(['me:read', 'me:write'])]
    #[ORM\Column(length: 180)]
    private string $email;

    /** @var list<string> The user roles */
    #[Groups(['me:read'])]
    #[ORM\Column]
    private array $roles = [];

    /** @var string The hashed password */
    #[ORM\Column]
    private string $password;

    #[Assert\NotBlank]
    #[Groups(['me:read', 'me:write', 'event_type:read'])]
    #[ORM\Column(length: 255)]
    private string $givenName;

    #[Assert\NotBlank]
    #[Groups(['me:read', 'me:write', 'event_type:read'])]
    #[ORM\Column(length: 255)]
    private string $familyName;

    /** @var Collection<int, EventType> */
    #[ORM\OneToMany(targetEntity: EventType::class, mappedBy: 'host')]
    private Collection $eventTypes;

    /** @var Collection<int, Unavailability> */
    #[ORM\OneToMany(targetEntity: Unavailability::class, mappedBy: 'user')]
    private Collection $unavailabilities;

    /** @var Collection<int, Availability> */
    #[ORM\OneToMany(targetEntity: Availability::class, mappedBy: 'user')]
    private Collection $availabilities;

    /** @var Collection<int, CalDavAuth> */
    #[ORM\OneToMany(targetEntity: CalDavAuth::class, mappedBy: 'user')]
    private Collection $calDavAuths;

    #[ORM\Column]
    private bool $enabled = false;

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column]
    private \DateTimeImmutable $updatedAt;

    public function __construct()
    {
        $this->eventTypes       = new ArrayCollection();
        $this->unavailabilities = new ArrayCollection();
        $this->availabilities   = new ArrayCollection();
        $this->calDavAuths      = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        /** @var non-empty-string $email */
        $email = $this->email;

        return $email;
    }

    /** @see UserInterface */
    public function getRoles(): array
    {
        $roles   = $this->roles;
        $roles[] = 'ROLE_USER';

        return \array_unique($roles);
    }

    /** @param list<string> $roles */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    public function addRole(string $role): static
    {
        if (!\in_array($role, $this->roles, true)) {
            $this->roles[] = $role;
        }

        return $this;
    }

    /** @see PasswordAuthenticatedUserInterface */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /** @see UserInterface */
    public function eraseCredentials(): void
    {
    }

    public function getGivenName(): string
    {
        return $this->givenName;
    }

    public function setGivenName(string $givenName): static
    {
        $this->givenName = $givenName;

        return $this;
    }

    public function getFamilyName(): string
    {
        return $this->familyName;
    }

    public function setFamilyName(string $familyName): static
    {
        $this->familyName = $familyName;

        return $this;
    }

    /** @return Collection<int, EventType> */
    public function getEventTypes(): Collection
    {
        return $this->eventTypes;
    }

    public function addEventType(EventType $eventType): static
    {
        if (!$this->eventTypes->contains($eventType)) {
            $this->eventTypes->add($eventType);
            $eventType->setHost($this);
        }

        return $this;
    }

    public function removeEventType(EventType $eventType): static
    {
        if ($this->eventTypes->removeElement($eventType)) {
            // set the owning side to null (unless already changed)
            if ($eventType->getHost() === $this) {
                $eventType->setHost(null);
            }
        }

        return $this;
    }

    /** @return Collection<int, Unavailability> */
    public function getUnavailabilities(): Collection
    {
        return $this->unavailabilities;
    }

    public function addUnavailability(Unavailability $recurringUnavailability): static
    {
        if (!$this->unavailabilities->contains($recurringUnavailability)) {
            $this->unavailabilities->add($recurringUnavailability);
            $recurringUnavailability->setUser($this);
        }

        return $this;
    }

    public function removeUnavailability(Unavailability $recurringUnavailability): static
    {
        if ($this->unavailabilities->removeElement($recurringUnavailability)) {
            // set the owning side to null (unless already changed)
            if ($recurringUnavailability->getUser() === $this) {
                $recurringUnavailability->setUser(null);
            }
        }

        return $this;
    }

    /** @return Collection<int, Availability> */
    public function getAvailabilities(): Collection
    {
        return $this->availabilities;
    }

    public function addAvailability(Availability $availability): static
    {
        if (!$this->availabilities->contains($availability)) {
            $this->availabilities->add($availability);
            $availability->setUser($this);
        }

        return $this;
    }

    public function removeAvailability(Availability $availability): static
    {
        if ($this->availabilities->removeElement($availability)) {
            // set the owning side to null (unless already changed)
            if ($availability->getUser() === $this) {
                $availability->setUser(null);
            }
        }

        return $this;
    }

    /** @return Collection<int, CalDavAuth> */
    public function getCalDavAuths(): Collection
    {
        return $this->calDavAuths;
    }

    public function addCalDavAuth(CalDavAuth $calDavAuth): static
    {
        if (!$this->calDavAuths->contains($calDavAuth)) {
            $this->calDavAuths->add($calDavAuth);
            $calDavAuth->setUser($this);
        }

        return $this;
    }

    public function removeCalDavAuth(CalDavAuth $calDavAuth): static
    {
        if ($this->calDavAuths->removeElement($calDavAuth)) {
            // set the owning side to null (unless already changed)
            if ($calDavAuth->getUser() === $this) {
                $calDavAuth->setUser(null);
            }
        }

        return $this;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): static
    {
        $this->enabled = $enabled;

        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    #[ORM\PrePersist]
    public function setCreatedAtValue(): void
    {
        $this->createdAt = new DateTimeImmutable();
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function setUpdatedAtValue(): void
    {
        $this->updatedAt = new DateTimeImmutable();
    }
}
