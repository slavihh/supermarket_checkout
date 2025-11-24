<?php

declare(strict_types=1);

namespace App\Tests\Service\Parser;

use App\Service\Parser\StringParserService;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class StringParserServiceTest extends TestCase
{
    private StringParserService $service;

    protected function setUp(): void
    {
        $this->service = new StringParserService();
    }

    public function testParseCountsCharactersCaseInsensitive(): void
    {
        $result = $this->service->parse('aAbB');

        $this->assertSame(['A' => 2, 'B' => 2], $result);
    }

    public function testParseThrowsOnEmptyString(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Items string must not be empty.');

        $this->service->parse('');
    }

    public function testParseThrowsOnInvalidCharacter(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid character "1" in input.');

        $this->service->parse('AB1');
    }
}
