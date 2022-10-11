<?php

namespace App\Repository;

use App\Entity\ShippingDelivery;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ShippingDelivery|null find($id, $lockMode = null, $lockVersion = null)
 * @method ShippingDelivery|null findOneBy(array $criteria, array $orderBy = null)
 * @method ShippingDelivery[]    findAll()
 * @method ShippingDelivery[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ShippingDeliveryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ShippingDelivery::class);
    }

    // /**
    //  * @return About[] Returns an array of About objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?About
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
