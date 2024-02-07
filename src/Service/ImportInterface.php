<?php

namespace App\Service;

use App\Entity\Member;
use App\Model\EpMember;
use Symfony\Contracts\HttpClient\ResponseInterface;

interface ImportInterface
{
    public function isStored(int $memberId): bool;

    public function storeNewEpMember(EpMember $member): Member;

    public function updateMemberData(EpMember $member): Member;

    public function getRawData(string $url) : ResponseInterface;
}