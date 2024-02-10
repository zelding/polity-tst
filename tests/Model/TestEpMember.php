<?php

namespace App\Tests\Model;

use App\Model\EpMember;

class TestEpMember extends EpMember
{
    public function __construct(
        ?int    $id,
        ?string $fullName,
        ?string $country,
        ?string $politicalGroup,
        ?string $nationalPoliticalGroup
    )
    {
        if ($id)  $this->setId($id);
        if ($fullName) $this->setFullName($fullName);
        if ($country) $this->setCountry($country);
        if ($politicalGroup) $this->setPoliticalGroup($politicalGroup);
        if ($nationalPoliticalGroup) $this->setNationalPoliticalGroup($nationalPoliticalGroup);
    }
}
