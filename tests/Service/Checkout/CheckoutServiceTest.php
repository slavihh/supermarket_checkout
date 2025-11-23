<?php

declare(strict_types=1);

namespace App\Tests\Service\Checkout;

use App\Dto\LineItemPriceResult;
use App\Entity\Product;
use App\Entity\Sale;
use App\Service\Calculator\PriceCalculatorService;
use App\Service\Calculator\PriceCalculatorServiceInterface;
use App\Service\Checkout\CheckoutService;
use App\Service\Product\ProductServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use RuntimeException;

final class CheckoutServiceTest extends TestCase
{
    private ProductServiceInterface|MockObject $productService;
    private PriceCalculatorServiceInterface|MockObject $priceCalculator;
    private EntityManagerInterface|MockObject $em;
    private CheckoutService $service;

    protected function setUp(): void
    {
        $this->productService = $this->createMock(ProductServiceInterface::class);
        $this->priceCalculator = $this->createMock(PriceCalculatorService::class);
        $this->em = $this->createMock(EntityManagerInterface::class);

        $this->service = new CheckoutService(
            $this->productService,
            $this->priceCalculator,
            $this->em
        );
    }

    public function testCheckoutSuccessful(): void
    {
        $itemsString = 'ABBA';

        $productA = $this->createMock(Product::class);
        $productA->method('getSku')->willReturn('A');
        $productA->method('getName')->willReturn('Apple');
        $productA->method('getUnitPrice')->willReturn(100);

        $productB = $this->createMock(Product::class);
        $productB->method('getSku')->willReturn('B');
        $productB->method('getName')->willReturn('Banana');
        $productB->method('getUnitPrice')->willReturn(50);

        $this->productService
            ->expects($this->once())
            ->method('findBySkus')
            ->with(['A', 'B'])
            ->willReturn([
                'A' => $productA,
                'B' => $productB,
            ]);

        $lineResultA = $this->createMock(LineItemPriceResult::class);
        $lineResultA->method('getProduct')->willReturn($productA);
        $lineResultA->method('getQuantity')->willReturn(2);
        $lineResultA->method('getLinePrice')->willReturn(190);
        $lineResultA->method('getAppliedPromotions')->willReturn(['PROMO_A']);

        $lineResultB = $this->createMock(LineItemPriceResult::class);
        $lineResultB->method('getProduct')->willReturn($productB);
        $lineResultB->method('getQuantity')->willReturn(2);
        $lineResultB->method('getLinePrice')->willReturn(90);
        $lineResultB->method('getAppliedPromotions')->willReturn(['PROMO_B']);

        $this->priceCalculator
            ->expects($this->exactly(2))
            ->method('calculate')
            ->willReturnMap([
                [$productA, 2, $lineResultA],
                [$productB, 2, $lineResultB],
            ]);

        $this->em
            ->expects($this->once())
            ->method('persist')
            ->with($this->isInstanceOf(Sale::class));

        $this->em
            ->expects($this->once())
            ->method('flush');

        $result = $this->service->checkout($itemsString);

        $this->assertArrayHasKey('sale', $result);
        $this->assertArrayHasKey('lineDetails', $result);
        $this->assertArrayHasKey('totalPrice', $result);

        $this->assertInstanceOf(Sale::class, $result['sale']);
        $this->assertSame(280, $result['totalPrice']);

        $sale = $result['sale'];
        $this->assertCount(2, $sale->getItems());
        $this->assertSame(280, $sale->getTotalPrice());

        $lineDetails = $result['lineDetails'];
        $this->assertCount(2, $lineDetails);

        $first = $lineDetails[0];
        $this->assertSame('A', $first['sku']);
        $this->assertSame('Apple', $first['name']);
        $this->assertSame(2, $first['quantity']);
        $this->assertSame(100, $first['unitPrice']);
        $this->assertSame(190, $first['linePrice']);
        $this->assertSame(['PROMO_A'], $first['appliedPromotions']);

        $second = $lineDetails[1];
        $this->assertSame('B', $second['sku']);
        $this->assertSame('Banana', $second['name']);
        $this->assertSame(2, $second['quantity']);
        $this->assertSame(50, $second['unitPrice']);
        $this->assertSame(90, $second['linePrice']);
        $this->assertSame(['PROMO_B'], $second['appliedPromotions']);
    }

    public function testCheckoutThrowsWhenUnknownSkuReturnedFromProductService(): void
    {
        $itemsString = 'AB';

        $productA = $this->createMock(Product::class);
        $productA->method('getSku')->willReturn('A');

        $this->productService
            ->expects($this->once())
            ->method('findBySkus')
            ->with(['A', 'B'])
            ->willReturn([
                'A' => $productA,
            ]);

        $this->priceCalculator
            ->expects($this->never())
            ->method('calculate');

        $this->em
            ->expects($this->never())
            ->method('persist');

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Unknown product SKU "B".');

        $this->service->checkout($itemsString);
    }

    public function testCheckoutThrowsOnEmptyItemsString(): void
    {
        $this->productService
            ->expects($this->never())
            ->method('findBySkus');

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Items string must not be empty.');

        $this->service->checkout('');
    }

    public function testCheckoutThrowsOnInvalidCharacter(): void
    {
        $this->productService
            ->expects($this->never())
            ->method('findBySkus');

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid character "1" in input.');

        $this->service->checkout('AB1');
    }

    public function testParseItemsStringIsCaseInsensitiveAndCountsCorrectly(): void
    {
        $reflection = new ReflectionClass(CheckoutService::class);
        $method = $reflection->getMethod('parseItemsString');

        $result = $method->invoke($this->service, 'aAbB');

        $this->assertSame(['A' => 2, 'B' => 2], $result);
    }
}
