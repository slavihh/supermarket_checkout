<?php

declare(strict_types=1);

namespace App\Service\Calculator;

use App\Dto\LineItemPriceResult;
use App\Entity\Product;

interface PriceCalculatorServiceInterface
{
    public function calculate(Product $product, int $quantity): LineItemPriceResult;
}
