<?php

namespace App\Repository;

use App\Classes\Filter;
use App\Classes\Search;
use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    /**
     * @var PaginatorInterface
     */
    private $paginator;
    public function __construct(ManagerRegistry $registry, PaginatorInterface $paginator)
    {
        parent::__construct($registry, Product::class);
        $this->paginator = $paginator;
    }

    public function findVisibleProducts()
    {
        return $this->createQueryBuilder('p')
            ->select('p', 'pics', 'cats')
            ->leftJoin('p.images', 'pics')
            ->leftJoin('p.catalogs', 'cats')
            ->where('p.isShow = 1')
            ->orderBy('p.id', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param Filter $filter
     * @param string|null $search
     * @param string|null $sub
     * @param int $pages
     * @return PaginationInterface
     */
    public function findSearch(Filter $filter, ?string $search, ?string $sub,int $pages):PaginationInterface
    {
        $query = $this->getSearchQuery($filter);
        if ($search){
            $query = $query
                ->andWhere('p.category = :string')
                ->setParameter('string', $search);
            if ($sub){
                $query = $query
                    ->andWhere('p.subCategory = :val')
                    ->setParameter('val', $sub);
            }
        }

        $query = $query->andWhere('p.isShow = 1')
            ->orderBy('p.id', 'DESC')
            ->getQuery();
        return $this->paginator->paginate(
            $query,
            $pages,
            6
        );
    }

    /**
     * @param Filter $filter
     * @return integer[]
     */
    public function findMinMax(Filter $filter): array
    {
        $results = $this->getSearchQuery($filter, true)
            ->select('MIN(p.price) as min, MAX(p.price) as max')
            ->getQuery()
            ->getScalarResult();

        return [$results[0]['min']/100, $results[0]['max']/100];
    }

    private function getSearchQuery(Filter $filter, $ignorePrice = false):QueryBuilder
    {
        $query = $this->createQueryBuilder('p');


        if (!empty($filter->min) && $ignorePrice === false){
            $query = $query
                ->andWhere('p.price >= :min')
                ->setParameter('min', $filter->min * 100);
        }

        if (!empty($filter->max) && $ignorePrice === false){
            $query = $query
                ->andWhere('p.price <= :max')
                ->setParameter('max', $filter->max * 100);
        }
        return $query;
    }

    // /**
    //  * @return Product[] Returns an array of Product objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Product
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
