<?php

namespace App\Model;

class EpMemberCollection
{
    /**
     * @var array<EpMember>
     */
    public array $meps = [];

    public function addMep($member): void
    {
        $this->meps[] = $member;
    }

    public function getMeps(): array
    {
        return $this->meps;
    }

    public function setMeps(array $meps): EpMemberCollection
    {
        $this->meps = $meps;
        return $this;
    }
}