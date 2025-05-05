<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\OrgRepository;
use App\State\OrgsProvider;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ApiResource(
    operations: [
        new GetCollection(
            normalizationContext: [
                'groups' => [
                    'org:read',
                ],
            ],
            provider: OrgsProvider::class,
        ),
        new Get(
            normalizationContext: [
                'groups' => [
                    'org:read',
                ],
            ],
            security: "is_granted('ORG_VIEW', object)",
        ),
    ],
)]
#[ORM\Entity(repositoryClass: OrgRepository::class)]
class Team
{
    #[Groups(['me:read', 'org:read'])]
    #[ORM\Column]
    #[ORM\GeneratedValue]
    #[ORM\Id]
    private ?int $id = null;

    #[Groups(['me:read', 'org:read'])]
    #[ORM\Column(length: 255)]
    private string $name;

    /** @var Collection<int, User> */
    #[ORM\JoinTable(name: 'user2team')]
    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'teams')]
    private Collection $members;

    /** @var Collection<int, User> */
    #[ORM\JoinTable(name: 'user2manage_team')]
    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'managedTeams')]
    private Collection $managers;

    #[Groups(['me:read', 'org:read'])]
    #[ORM\Column(length: 255, unique: true)]
    private string $slug;

    public function __construct()
    {
        $this->members  = new ArrayCollection();
        $this->managers = new ArrayCollection();
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

    /** @return Collection<int, User> */
    public function getMembers(): Collection
    {
        return $this->members;
    }

    public function addMember(User $member): static
    {
        if (!$this->members->contains($member)) {
            $this->members->add($member);
        }

        return $this;
    }

    public function removeMember(User $member): static
    {
        $this->members->removeElement($member);

        return $this;
    }

    /** @return Collection<int, User> */
    public function getManagers(): Collection
    {
        return $this->managers;
    }

    public function addManager(User $manager): static
    {
        if (!$this->managers->contains($manager)) {
            $this->managers->add($manager);
        }

        return $this;
    }

    public function removeManager(User $manager): static
    {
        $this->managers->removeElement($manager);

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }
}
