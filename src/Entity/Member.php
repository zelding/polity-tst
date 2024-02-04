<?php

namespace App\Entity;

use App\Repository\MemberRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;

#[ORM\Entity(repositoryClass: MemberRepository::class)]
class Member implements \JsonSerializable
{
    #[ORM\Id]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $fullName = null;

    #[ORM\Column(length: 255)]
    private ?string $country = null;

    #[ORM\Column(length: 255)]
    private ?string $epPoliticalGroup = null;

    #[ORM\Column(length: 255)]
    private ?string $nationalPoliticalGroup = null;

    #[ORM\OneToMany(targetEntity: MemberContact::class, mappedBy: "member", fetch: "EXTRA_LAZY", orphanRemoval: true)]
    private Collection $contacts;

    public function jsonSerialize(): array
    {
        $base = [
            'id'             => $this->id,
            'firstName'      => $this->fullName,
            'lastName'       => $this->fullName,
            'country'        => $this->country,
            'politicalGroup' => $this->epPoliticalGroup
        ];

        // this is where I would install the PHPLeague/Fractal - but I'M afraid it is not mainstream enough
        if( $this->contacts instanceof PersistentCollection && $this->contacts->isInitialized()) {
            $base['contacts'] = $this->contacts->toArray();
        }

        return $base;
    }

    public function setId(int $id) :static
    {
        $this->id = $id;
        return $this;
    }

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
        return $this->epPoliticalGroup;
    }

    public function setEpPoliticalGroup(string $epPoliticalGroup): static
    {
        $this->epPoliticalGroup = $epPoliticalGroup;

        return $this;
    }

    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    public function setFullName(string $fullName): static
    {
        $this->fullName = $fullName;

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

    public function getContacts(): Collection
    {
        return $this->contacts;
    }
}
