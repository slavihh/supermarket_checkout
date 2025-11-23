<?php

declare(strict_types=1);

namespace App\Service\Calculator;

use App\Dto\LineItemPriceResult;
use App\Entity\Product;
use App\Entity\Promotion;
use InvalidArgumentException;

class PriceCalculatorService implements PriceCalculatorServiceInterface
{
    public function calculate(Product $product, int $quantity): LineItemPriceResult
    {
        if ($quantity <= 0) {
            throw new InvalidArgumentException('Quantity must be positive.');
        }

        if ($product->getPromotions()->isEmpty()) {
            $linePrice = $product->getUnitPrice() * $quantity;

            return new LineItemPriceResult(
                product: $product,
                quantity: $quantity,
                linePrice: $linePrice,
                appliedPromotions: []
            );
        }

        /** @var Promotion[] $promotions */
        $promotions = $product->getPromotions()->toArray();

        // pick promotion with best per-unit value
        usort($promotions, static function (Promotion $a, Promotion $b) {
            $unitA = $a->getSpecialPrice() / $a->getQuantity();
            $unitB = $b->getSpecialPrice() / $b->getQuantity();

            return $unitA <=> $unitB;
        });

        $best = $promotions[0];
        $n = $best->getQuantity();
        $specialPrice = $best->getSpecialPrice();

        $groups = intdiv($quantity, $n);
        $remainder = $quantity % $n;

        $linePrice = $groups * $specialPrice + $remainder * $product->getUnitPrice();

        $appliedPromotions = [];
        if ($groups > 0) {
            $appliedPromotions[] = \sprintf(
                '%d × (%d for %d)',
                $groups,
                $n,
                $specialPrice
            );
        }

        return new LineItemPriceResult(
            product: $product,
            quantity: $quantity,
            linePrice: $linePrice,
            appliedPromotions: $appliedPromotions
        );
    }
}
