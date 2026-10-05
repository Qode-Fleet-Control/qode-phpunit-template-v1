<?php

declare(strict_types=1);

namespace Qode\PhpunitTemplate\Tests;

use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Qode\PhpunitTemplate\Calculator;

#[CoversClass(Calculator::class)]
final class CalculatorTest extends TestCase
{
    private Calculator $calculator;

    protected function setUp(): void
    {
        $this->calculator = new Calculator();
    }

    /** @return array<string, array{int|float, int|float, int|float}> */
    public static function additionProvider(): array
    {
        return [
            'zeros'     => [0, 0, 0],
            'positives' => [1, 2, 3],
            'negative'  => [-1, 1, 0],
            'floats'    => [0.5, 0.25, 0.75],
        ];
    }

    #[DataProvider('additionProvider')]
    public function testAdd(int|float $a, int|float $b, int|float $expected): void
    {
        $this->assertSame($expected, $this->calculator->add($a, $b));
    }

    public function testDivide(): void
    {
        $this->assertSame(2.5, $this->calculator->divide(5, 2));
    }

    public function testDivideByZeroThrows(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Division by zero');

        $this->calculator->divide(1, 0);
    }
}
