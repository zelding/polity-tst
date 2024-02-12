<?php

namespace App\Model;

use JetBrains\PhpStorm\Deprecated;

class EpMember
{
    protected int    $id;
    protected string $fullName;
    protected string $country;
    protected string $politicalGroup;
    protected string $nationalPoliticalGroup;
    /** @var array<string> */
    protected array $nameParts;

    public function __construct()
    {
    }

    public function getNameParts(): array
    {
        return $this->nameParts;
    }

    public function setNameParts(array $nameParts): static
    {
        $this->nameParts = $nameParts;

        return $this;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;
        return $this;
    }

    /** @deprecated */
    public function getFullName(): string
    {
        return $this->fullName;
    }

    /** @deprecated */
    public function setFullName(string $fullName): static
    {
        $this->fullName = $fullName;
        return $this;
    }

    public function getCountry(): string
    {
        return $this->country;
    }

    public function setCountry(string $country): static
    {
        $this->country = $country;
        return $this;
    }

    public function getPoliticalGroup(): string
    {
        return $this->politicalGroup;
    }

    public function setPoliticalGroup(string $politicalGroup): static
    {
        $this->politicalGroup = $politicalGroup;
        return $this;
    }

    public function getNationalPoliticalGroup(): string
    {
        return $this->nationalPoliticalGroup;
    }

    public function setNationalPoliticalGroup(string $nationalPoliticalGroup): static
    {
        $this->nationalPoliticalGroup = $nationalPoliticalGroup;
        return $this;
    }
}
