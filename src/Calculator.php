<?php

declare(strict_types=1);

namespace Qode\PhpunitTemplate;

use InvalidArgumentException;

final class Calculator
{
    public function add(int|float $a, int|float $b): int|float
    {
        return $a + $b;
    }

    public function divide(int|float $a, int|float $b): float
    {
        if ($b == 0) {
            throw new InvalidArgumentException('Division by zero');
        }

        return $a / $b;
    }
}
