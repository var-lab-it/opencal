<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use App\Repository\UserRepository;
use App\State\CurrentUserProvider;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Attribute\Groups;

#[ApiResource(
    operations: [
        new Get(
            uriTemplate: '/me',
            normalizationContext: [
                'groups' => [
                    'me:read',
                ],
            ],
            security: "is_granted('IS_AUTHENTICATED_FULLY')",
            provider: CurrentUserProvider::class,
        ),
    ],
)]
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[Groups(['me:read'])]
    #[ORM\Column]
    #[ORM\GeneratedValue]
    #[ORM\Id]
    private ?int $id = null;

    #[Groups(['me:read'])]
    #[ORM\Column(length: 180)]
    private string $email;

    /** @var list<string> The user roles */
    #[Groups(['me:read'])]
    #[ORM\Column]
    private array $roles = [];

    /** @var string The hashed password */
    #[ORM\Column]
    private string $password;

    #[Groups(['me:read'])]
    #[ORM\Column(length: 255)]
    private string $givenName;

    #[Groups(['me:read'])]
    #[ORM\Column(length: 255)]
    private string $familyName;

    /** @var Collection<int, Team> */
    #[Groups(['me:read'])]
    #[ORM\ManyToMany(targetEntity: Team::class, mappedBy: 'members')]
    private Collection $teams;

    /** @var Collection<int, Team> */
    #[Groups(['me:read'])]
    #[ORM\ManyToMany(targetEntity: Team::class, mappedBy: 'managers')]
    private Collection $managedTeams;

    public function __construct()
    {
        $this->teams        = new ArrayCollection();
        $this->managedTeams = new ArrayCollection();
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
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
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

    /** @return Collection<int, Team> */
    public function getTeams(): Collection
    {
        return $this->teams;
    }

    public function addOrg(Team $org): static
    {
        if (!$this->teams->contains($org)) {
            $this->teams->add($org);
            $org->addMember($this);
        }

        return $this;
    }

    public function removeOrg(Team $org): static
    {
        if ($this->teams->removeElement($org)) {
            $org->removeMember($this);
        }

        return $this;
    }

    /** @return Collection<int, Team> */
    public function getManagedTeams(): Collection
    {
        return $this->managedTeams;
    }

    public function addManagedOrg(Team $managedOrg): static
    {
        if (!$this->managedTeams->contains($managedOrg)) {
            $this->managedTeams->add($managedOrg);
            $managedOrg->addManager($this);
        }

        return $this;
    }

    public function removeManagedOrg(Team $managedOrg): static
    {
        if ($this->managedTeams->removeElement($managedOrg)) {
            $managedOrg->removeManager($this);
        }

        return $this;
    }
}
