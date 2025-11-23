<?php

declare(strict_types=1);

namespace App\Dto;

use App\Entity\Product;

class LineItemPriceResult
{
    /**
     * @param string[] $appliedPromotions
     */
    public function __construct(
        private readonly Product $product,
        private readonly int $quantity,
        private readonly int $linePrice,
        private readonly array $appliedPromotions = [],
    ) {
    }

    public function getProduct(): Product
    {
        return $this->product;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function getLinePrice(): int
    {
        return $this->linePrice;
    }

    /**
     * @return string[]
     */
    public function getAppliedPromotions(): array
    {
        return $this->appliedPromotions;
    }
}
