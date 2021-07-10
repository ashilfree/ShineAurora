<?php

namespace App\Repository;

use App\Entity\DiscountBanner;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method DiscountBanner|null find($id, $lockMode = null, $lockVersion = null)
 * @method DiscountBanner|null findOneBy(array $criteria, array $orderBy = null)
 * @method DiscountBanner[]    findAll()
 * @method DiscountBanner[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DiscountBannerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DiscountBanner::class);
    }

    // /**
    //  * @return DiscountBanner[] Returns an array of DiscountBanner objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?DiscountBanner
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
