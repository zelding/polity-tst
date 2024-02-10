<?php

namespace App\Service;

use App\Entity\Member;
use App\Exception\AppException;
use App\Model\EpMember;
use App\Repository\MemberRepository;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class ImportService implements ImportInterface
{
    public function __construct(
        private readonly MemberRepository       $memberRepository,
        private readonly HttpClientInterface    $httpClient,
    )
    {}

    public function isStored(int $memberId): bool
    {
        return null !== $this->memberRepository->find($memberId);
    }

    /** @throws  AppException */
    public function storeNewEpMember(EpMember $member): Member
    {
        if ( $this->isStored($member->getId()) ) {
            throw new AppException("Already exists", 417);
        }

        return $this->memberRepository->storeNewEpMember($member);
    }

    /** @throws  AppException */
    public function updateMemberData(EpMember $member): Member
    {
        if ( !$this->isStored($member->getId()) ) {
            throw new AppException("Not found: {$member->getId()}", 417);
        }

        return $this->memberRepository->updateMemberData($member);
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function getRawData(string $url) : ResponseInterface
    {
        return $this->httpClient->request("GET", $url);
    }

    public function parseRawData($content): EpMember
    {
        $content = new \SimpleXMLElement($content);

        $content = (array)$content;

        $memberModel = new EpMember();
        $memberModel->setId($content['id'])
                    ->setFullName($content['fullName'])
                    ->setCountry($content['country'])
                    ->setPoliticalGroup($content['politicalGroup'])
                    ->setNationalPoliticalGroup($content['nationalPoliticalGroup']);

        return $memberModel;
    }
}
