<?php

declare(strict_types=1);

namespace App\Service\Parser;

interface ParserServiceInterface
{
    /**
     * @return array<string, int>
     */
    public function parse(string $itemsString): array;
}
