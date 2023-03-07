<?php

declare(strict_types=1);

namespace ComissionCalculator\tests\Views;

use PHPUnit\Framework\TestCase;
use ComissionCalculator\Views\SimpleView;

final class SimpleViewTest extends TestCase
{
    public function testEmpty(): void
    {
        $this->expectOutputString('');
        print SimpleView::print([]);
    }

    public function testOneRow(): void
    {
        $this->expectOutputString('1.2');
        print SimpleView::print([1.2]);
    }

    public function testTwoRows(): void
    {
        $this->expectOutputString(sprintf('0.03%s123', PHP_EOL));
        print SimpleView::print([0.03, 123]);
    }

    public function testThreeRows(): void
    {
        $this->expectOutputString(sprintf('0%1$s5.7%1$s0.08', PHP_EOL));
        print SimpleView::print([0, 5.7, 0.08]);
    }

    public function testStrings(): void
    {
        $this->expectOutputString(sprintf('test%1$s123%1$s{}', PHP_EOL));
        print SimpleView::print(['test', '123', '{}']);
    }
}
