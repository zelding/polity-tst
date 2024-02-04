<?php

namespace App\Entity;

use App\Repository\MemberRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MemberRepository::class)]
class Member
{
    #[ORM\Id]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $FullName = null;

    #[ORM\Column(length: 255)]
    private ?string $country = null;

    #[ORM\Column(length: 255)]
    private ?string $EpPoliticalGroup = null;

    #[ORM\Column(length: 255)]
    private ?string $nationalPoliticalGroup = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(string $country): static
    {
        $this->country = $country;

        return $this;
    }

    public function getEpPoliticalGroup(): ?string
    {
        return $this->EpPoliticalGroup;
    }

    public function setEpPoliticalGroup(string $EpPoliticalGroup): static
    {
        $this->EpPoliticalGroup = $EpPoliticalGroup;

        return $this;
    }

    public function getFullName(): ?string
    {
        return $this->FullName;
    }

    public function setFullName(string $FullName): static
    {
        $this->FullName = $FullName;

        return $this;
    }

    public function getNationalPoliticalGroup(): ?string
    {
        return $this->nationalPoliticalGroup;
    }

    public function setNationalPoliticalGroup(?string $nationalPoliticalGroup): static
    {
        $this->nationalPoliticalGroup = $nationalPoliticalGroup;
        return $this;
    }
}
