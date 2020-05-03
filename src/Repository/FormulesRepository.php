<?php

namespace App\Repository;

use App\Entity\Formules;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Formules|null find($id, $lockMode = null, $lockVersion = null)
 * @method Formules|null findOneBy(array $criteria, array $orderBy = null)
 * @method Formules[]    findAll()
 * @method Formules[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FormulesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Formules::class);
    }

    // /**
    //  * @return Formules[] Returns an array of Formules objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('f.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Formules
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
