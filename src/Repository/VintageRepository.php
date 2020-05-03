<?php

namespace App\Repository;

use App\Entity\Vintage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Vintage|null find($id, $lockMode = null, $lockVersion = null)
 * @method Vintage|null findOneBy(array $criteria, array $orderBy = null)
 * @method Vintage[]    findAll()
 * @method Vintage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VintageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Vintage::class);
    }

    // /**
    //  * @return Vintage[] Returns an array of Vintage objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('v.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Vintage
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
