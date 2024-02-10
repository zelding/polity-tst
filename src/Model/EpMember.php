<?php

namespace App\Model;

class EpMember
{
    public int    $id;
    public string $fullName;
    public string $country;
    public string $politicalGroup;
    public string $nationalPoliticalGroup;

    public function __construct()
    {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): EpMember
    {
        $this->id = $id;
        return $this;
    }

    public function getFullName(): string
    {
        return $this->fullName;
    }

    public function setFullName(string $fullName): EpMember
    {
        $this->fullName = $fullName;
        return $this;
    }

    public function getCountry(): string
    {
        return $this->country;
    }

    public function setCountry(string $country): EpMember
    {
        $this->country = $country;
        return $this;
    }

    public function getPoliticalGroup(): string
    {
        return $this->politicalGroup;
    }

    public function setPoliticalGroup(string $politicalGroup): EpMember
    {
        $this->politicalGroup = $politicalGroup;
        return $this;
    }

    public function getNationalPoliticalGroup(): string
    {
        return $this->nationalPoliticalGroup;
    }

    public function setNationalPoliticalGroup(string $nationalPoliticalGroup): EpMember
    {
        $this->nationalPoliticalGroup = $nationalPoliticalGroup;
        return $this;
    }
}
