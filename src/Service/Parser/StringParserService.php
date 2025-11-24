<?php

declare(strict_types=1);

namespace App\Service\Parser;

use InvalidArgumentException;

class StringParserService implements ParserServiceInterface
{
    /**
     * @return array<string, int>
     */
    public function parse(string $itemsString): array
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
