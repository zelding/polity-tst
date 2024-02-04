<?php

namespace App\Repository;

use App\Entity\MemberContact;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MemberContact>
 *
 * @method MemberContact|null find($id, $lockMode = null, $lockVersion = null)
 * @method MemberContact|null findOneBy(array $criteria, array $orderBy = null)
 * @method MemberContact[]    findAll()
 * @method MemberContact[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MemberContactRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MemberContact::class);
    }

//    /**
//     * @return MemberContact[] Returns an array of MemberContact objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('m.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?MemberContact
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
