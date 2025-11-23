<?php

declare(strict_types=1);

namespace App\Service\Product;

use App\Entity\Product;
use App\Repository\ProductRepository;

readonly class ProductService implements ProductServiceInterface
{
    public function __construct(private ProductRepository $productRepository)
    {
    }

    /**
     * @param array<int, string> $skus
     *
     * @return array<string, Product>
     */
    public function findBySkus(array $skus): array
    {
        return $this->productRepository->findBySkusIndexedBySku($skus);
    }
}
