<?php

declare(strict_types=1);

namespace App\Service\Checkout;

use App\Dto\LineItemPriceResult;
use App\Entity\Sale;
use App\Entity\SaleItem;
use App\Service\Calculator\PriceCalculator;
use App\Service\Product\ProductServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;
use RuntimeException;

readonly class CheckoutService implements CheckoutServiceInterface
{
    public function __construct(
        private ProductServiceInterface $productService,
        private PriceCalculator $priceCalculator,
        private EntityManagerInterface $em,
    ) {
    }

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
    public function checkout(string $itemsString): array
    {
        $skuCounts = $this->parseItemsString($itemsString);
        // load products from cache or db
        $productsBySku = $this->productService->findBySkus(array_keys($skuCounts));
        foreach ($skuCounts as $sku => $_) {
            if (!isset($productsBySku[$sku])) {
                throw new RuntimeException(\sprintf('Unknown product SKU "%s".', $sku));
            }
        }

        /** @var LineItemPriceResult[] $lineResults */
        $lineResults = [];
        foreach ($skuCounts as $sku => $qty) {
            $product = $productsBySku[$sku];
            $lineResults[] = $this->priceCalculator->calculate($product, $qty);
        }

        $sale = new Sale();
        $total = 0;
        $lineDetails = [];

        foreach ($lineResults as $result) {
            $product = $result->getProduct();

            $item = new SaleItem();
            $item->setProduct($product);
            $item->setQuantity($result->getQuantity());
            $item->setLinePrice($result->getLinePrice());

            $sale->addItem($item);
            $total += $result->getLinePrice();

            $lineDetails[] = [
                'sku' => $product->getSku(),
                'name' => $product->getName(),
                'quantity' => $result->getQuantity(),
                'unitPrice' => $product->getUnitPrice(),
                'linePrice' => $result->getLinePrice(),
                'appliedPromotions' => $result->getAppliedPromotions(),
            ];
        }

        $sale->setTotalPrice($total);

        $this->em->persist($sale);
        $this->em->flush();

        return [
            'sale' => $sale,
            'lineDetails' => $lineDetails,
            'totalPrice' => $total,
        ];
    }

    /**
     * @return array<string, int>
     */
    private function parseItemsString(string $itemsString): array
    {
        $itemsString = strtoupper(trim($itemsString));

        if ('' === $itemsString) {
            throw new InvalidArgumentException('Items string must not be empty.');
        }

        $counts = [];

        foreach (str_split($itemsString) as $char) {
            if (!ctype_alpha($char)) {
                throw new InvalidArgumentException(\sprintf('Invalid character "%s" in input.', $char));
            }

            $counts[$char] = ($counts[$char] ?? 0) + 1;
        }

        return $counts;
    }
}
