<?php

namespace App\Repository;

use App\Entity\QualificationSkillEmployee;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method QualificationSkillEmployee|null find($id, $lockMode = null, $lockVersion = null)
 * @method QualificationSkillEmployee|null findOneBy(array $criteria, array $orderBy = null)
 * @method QualificationSkillEmployee[]    findAll()
 * @method QualificationSkillEmployee[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QualificationSkillEmployeeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, QualificationSkillEmployee::class);
    }

    // /**
    //  * @return QualificationSkillEmployee[] Returns an array of QualificationSkillEmployee objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('q')
            ->andWhere('q.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('q.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?QualificationSkillEmployee
    {
        return $this->createQueryBuilder('q')
            ->andWhere('q.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
