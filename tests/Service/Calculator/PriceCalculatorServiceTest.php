<?php

declare(strict_types=1);

namespace App\Tests\Service\Calculator;

use App\Dto\LineItemPriceResult;
use App\Entity\Product;
use App\Entity\Promotion;
use App\Service\Calculator\PriceCalculatorService;
use App\Service\Calculator\PriceCalculatorServiceInterface;
use Doctrine\Common\Collections\ArrayCollection;
use InvalidArgumentException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class PriceCalculatorServiceTest extends TestCase
{
    private PriceCalculatorServiceInterface $calculator;

    protected function setUp(): void
    {
        $this->calculator = new PriceCalculatorService();
    }

    public function testCalculateThrowsOnNonPositiveQuantity(): void
    {
        /** @var Product|MockObject $product */
        $product = $this->createMock(Product::class);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Quantity must be positive.');

        $this->calculator->calculate($product, 0);
    }

    public function testCalculateWithoutPromotions(): void
    {
        /** @var Product|MockObject $product */
        $product = $this->createMock(Product::class);
        $product->method('getUnitPrice')->willReturn(100);
        $product->method('getPromotions')->willReturn(new ArrayCollection());

        $result = $this->calculator->calculate($product, 3);

        $this->assertInstanceOf(LineItemPriceResult::class, $result);
        $this->assertSame($product, $result->getProduct());
        $this->assertSame(3, $result->getQuantity());
        $this->assertSame(300, $result->getLinePrice());
        $this->assertSame([], $result->getAppliedPromotions());
    }

    public function testCalculateWithSinglePromotionExactGroups(): void
    {
        /** @var Product|MockObject $product */
        $product = $this->createMock(Product::class);
        $product->method('getUnitPrice')->willReturn(100);

        /** @var Promotion|MockObject $promotion */
        $promotion = $this->createMock(Promotion::class);
        $promotion->method('getQuantity')->willReturn(3);
        $promotion->method('getSpecialPrice')->willReturn(250);

        $product
            ->method('getPromotions')
            ->willReturn(new ArrayCollection([$promotion]));

        $result = $this->calculator->calculate($product, 6);

        $this->assertSame($product, $result->getProduct());
        $this->assertSame(6, $result->getQuantity());
        $this->assertSame(500, $result->getLinePrice());
        $this->assertSame(['2 × (3 for 250)'], $result->getAppliedPromotions());
    }

    public function testCalculateWithSinglePromotionWithRemainder(): void
    {
        /** @var Product|MockObject $product */
        $product = $this->createMock(Product::class);
        $product->method('getUnitPrice')->willReturn(100);

        /** @var Promotion|MockObject $promotion */
        $promotion = $this->createMock(Promotion::class);
        $promotion->method('getQuantity')->willReturn(3);
        $promotion->method('getSpecialPrice')->willReturn(250);

        $product
            ->method('getPromotions')
            ->willReturn(new ArrayCollection([$promotion]));

        $result = $this->calculator->calculate($product, 7);

        $this->assertSame($product, $result->getProduct());
        $this->assertSame(7, $result->getQuantity());
        $this->assertSame(600, $result->getLinePrice());
        $this->assertSame(['2 × (3 for 250)'], $result->getAppliedPromotions());
    }

    public function testCalculateWithMultiplePromotionsChoosesBestUnitPrice(): void
    {
        /** @var Product|MockObject $product */
        $product = $this->createMock(Product::class);
        $product->method('getUnitPrice')->willReturn(100);

        /** @var Promotion|MockObject $promoA */
        $promoA = $this->createMock(Promotion::class);
        $promoA->method('getQuantity')->willReturn(3);
        $promoA->method('getSpecialPrice')->willReturn(250); // ~83.33 per unit

        /** @var Promotion|MockObject $promoB */
        $promoB = $this->createMock(Promotion::class);
        $promoB->method('getQuantity')->willReturn(5);
        $promoB->method('getSpecialPrice')->willReturn(400); // 80 per unit (better)

        $product
            ->method('getPromotions')
            ->willReturn(new ArrayCollection([$promoA, $promoB]));

        $result = $this->calculator->calculate($product, 10);

        $this->assertSame($product, $result->getProduct());
        $this->assertSame(10, $result->getQuantity());
        $this->assertSame(800, $result->getLinePrice());
        $this->assertSame(['2 × (5 for 400)'], $result->getAppliedPromotions());
    }

    public function testCalculateWithPromotionButQuantityLessThanGroup(): void
    {
        /** @var Product|MockObject $product */
        $product = $this->createMock(Product::class);
        $product->method('getUnitPrice')->willReturn(100);

        /** @var Promotion|MockObject $promotion */
        $promotion = $this->createMock(Promotion::class);
        $promotion->method('getQuantity')->willReturn(3);
        $promotion->method('getSpecialPrice')->willReturn(250);

        $product
            ->method('getPromotions')
            ->willReturn(new ArrayCollection([$promotion]));

        $result = $this->calculator->calculate($product, 2);

        $this->assertSame($product, $result->getProduct());
        $this->assertSame(2, $result->getQuantity());
        $this->assertSame(200, $result->getLinePrice());
        $this->assertSame([], $result->getAppliedPromotions());
    }
}
