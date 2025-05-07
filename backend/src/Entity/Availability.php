<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\AvailabilityRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AvailabilityRepository::class)]
class Availability
{
    #[ORM\Column]
    #[ORM\GeneratedValue]
    #[ORM\Id]
    private int $id;

    #[ORM\Column(length: 255)]
    private string $dayOfWeek;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private \DateTime $startTime;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private \DateTime $endTime;

    #[ORM\JoinColumn(nullable: false)]
    #[ORM\ManyToOne(inversedBy: 'availabilities')]
    private User $user;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDayOfWeek(): string
    {
        return $this->dayOfWeek;
    }

    public function setDayOfWeek(string $dayOfWeek): static
    {
        $this->dayOfWeek = $dayOfWeek;

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

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        if ($user instanceof User) {
            $this->user = $user;
        }

        return $this;
    }
}
