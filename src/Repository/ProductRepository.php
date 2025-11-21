<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Product>
 */
final class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function save(Product $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Product $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findOneBySku(string $sku): ?Product
    {
        return $this->findOneBy(['sku' => strtoupper($sku)]);
    }

    /**
     * @param string[] $skus
     * @return array<string, Product> map sku => Product
     */
    public function getBySkusIndexedBySku(array $skus): array
    {
        if ($skus === []) {
            return [];
        }

        $qb = $this->createQueryBuilder('p')
            ->andWhere('p.sku IN (:skus)')
            ->setParameter('skus', $skus);

        /** @var Product[] $results */
        $results = $qb->getQuery()->getResult();

        $bySku = [];
        foreach ($results as $product) {
            $bySku[$product->getSku()] = $product;
        }

        return $bySku;
    }
}
