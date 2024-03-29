<?php

namespace App\Repository;

use App\Entity\Myimage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Myimage|null find($id, $lockMode = null, $lockVersion = null)
 * @method Myimage|null findOneBy(array $criteria, array $orderBy = null)
 * @method Myimage[]    findAll()
 * @method Myimage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MyimageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Myimage::class);
    }

    // /**
    //  * @return Myimage[] Returns an array of Myimage objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Myimage
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
