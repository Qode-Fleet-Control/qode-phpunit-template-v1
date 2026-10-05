<?php

declare(strict_types=1);

namespace Qode\PhpunitTemplate\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Qode\PhpunitTemplate\Greeter;

#[CoversClass(Greeter::class)]
final class GreeterTest extends TestCase
{
    public function testGreetsWithName(): void
    {
        $greeter = new Greeter();

        $greeting = $greeter->greet('Alice');

        $this->assertSame('Hello, Alice!', $greeting);
    }
}
