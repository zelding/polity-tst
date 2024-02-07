<?php

namespace App\Service;

use App\Entity\Member;
use App\Model\EpMember;
use App\Repository\MemberRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class ImportService implements ImportInterface
{
    public function __construct(
        private readonly MemberRepository       $memberRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly HttpClientInterface    $httpClient,
    )
    {}

    public function isStored(int $memberId): bool
    {
        return null !== $this->memberRepository->find($memberId);
    }

    public function storeNewEpMember(EpMember $member): Member
    {
        $entity = new Member();
        $entity->setId($member->getId())
            ->setFullName($member->getFullName())
            ->setCountry($member->getCountry())
            ->setEpPoliticalGroup($member->getPoliticalGroup())
            ->setNationalPoliticalGroup($member->getNationalPoliticalGroup());

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return $entity;
    }

    public function updateMemberData(EpMember $member): Member
    {
        $entity = $this->memberRepository->find($member->getId());

        $entity->setCountry($member->getCountry())
            ->setFullName($member->getFullName())
            ->setCountry($member->getCountry())
            ->setEpPoliticalGroup($member->getPoliticalGroup())
            ->setNationalPoliticalGroup($member->getNationalPoliticalGroup());

        $this->entityManager->flush();

        return $entity;
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function getRawData(string $url) : ResponseInterface
    {
        return $this->httpClient->request("GET", $url);
    }
}
