<?php

declare(strict_types=1);

namespace App\Service\Product;

use App\Entity\Product;

interface ProductServiceInterface
{
    /**
     * @param array<int, string> $skus
     *
     * @return array<string, Product>
     */
    public function findBySkus(array $skus): array;
}
