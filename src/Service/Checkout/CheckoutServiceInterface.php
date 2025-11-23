<?php

declare(strict_types=1);

namespace App\Service\Checkout;

use App\Entity\Sale;

interface CheckoutServiceInterface
{
    /**
     * @return array{
     *     sale: Sale,
     *     lineDetails: array<int, array{
     *         sku: string,
     *         name: string,
     *         quantity: int,
     *         unitPrice: int,
     *         linePrice: int,
     *         appliedPromotions: string[]
     *     }>,
     *     totalPrice: int
     * }
     */
    public function checkout(string $itemsString): array;
}
