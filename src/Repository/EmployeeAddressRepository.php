<?php

namespace App\Repository;

use App\Entity\EmployeeAddress;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method EmployeeAddress|null find($id, $lockMode = null, $lockVersion = null)
 * @method EmployeeAddress|null findOneBy(array $criteria, array $orderBy = null)
 * @method EmployeeAddress[]    findAll()
 * @method EmployeeAddress[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EmployeeAddressRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EmployeeAddress::class);
    }

    // /**
    //  * @return EmployeeAddress[] Returns an array of EmployeeAddress objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?EmployeeAddress
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
