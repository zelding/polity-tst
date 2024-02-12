<?php

namespace App\Repository;

use App\Entity\Member;
use App\Model\EpMember;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Member>
 *
 * @method Member|null find($id, $lockMode = null, $lockVersion = null)
 * @method Member|null findOneBy(array $criteria, array $orderBy = null)
 * @method Member[]    findAll()
 * @method Member[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MemberRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Member::class);
    }

    public function storeNewEpMember(EpMember $member): Member
    {
        $entity = new Member();
        $entity->setId($member->getId())
               ->setFullName($member->getFullName())
               ->setFirstName($member->getNameParts()[0])
               ->setLastName($member->getNameParts()[1])
               ->setCountry($member->getCountry())
               ->setEpPoliticalGroup($member->getPoliticalGroup())
               ->setNationalPoliticalGroup($member->getNationalPoliticalGroup());

        $this->getEntityManager()->beginTransaction();
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();
        $this->getEntityManager()->commit();

        return $entity;
    }

    public function updateMemberData(EpMember $member): Member
    {
        $entity = $this->find($member->getId());

        $entity->setCountry($member->getCountry())
               ->setFullName($member->getFullName())
               ->setFirstName($member->getNameParts()[0])
               ->setLastName($member->getNameParts()[1])
               ->setCountry($member->getCountry())
               ->setEpPoliticalGroup($member->getPoliticalGroup())
               ->setNationalPoliticalGroup($member->getNationalPoliticalGroup());

        $this->getEntityManager()->beginTransaction();
        $this->getEntityManager()->flush();
        $this->getEntityManager()->commit();

        return $entity;
    }

    public function findByIdWithContacts(int $id): array
    {
        return $this->createQueryBuilder('m')
            ->select('m, c')
            ->leftJoin('m.contacts', 'c')
            ->andWhere('m.id = :val')
            ->setParameter('val', $id)
            ->getQuery()
            ->getResult()
        ;
    }

}
