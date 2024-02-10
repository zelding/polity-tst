<?php

namespace App\Service;

use App\Entity\Member;
use App\Exception\AppException;
use App\Model\EpMember;
use Symfony\Contracts\HttpClient\ResponseInterface;

interface ImportInterface
{
    public function isStored(int $memberId): bool;

    /** @throws  AppException */
    public function storeNewEpMember(EpMember $member): Member;

    /** @throws  AppException */
    public function updateMemberData(EpMember $member): Member;

    public function getRawData(string $url) : ResponseInterface;

    public function parseRawData($content): EpMember;
}
