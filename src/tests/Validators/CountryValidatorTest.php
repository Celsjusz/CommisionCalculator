<?php

declare(strict_types=1);

namespace ComissionCalculator\tests\Validators;

use PHPUnit\Framework\TestCase;
use ComissionCalculator\Validators\CountryValidator;
use ComissionCalculator\Validators\CountryValidatorInterface;

final class CountryValidatorTest extends TestCase
{
    protected CountryValidatorInterface $class;

    public function setUp(): void
    {
        $this->class = new CountryValidator(['AB', 'BC', 'XY']);
    }

    public function testEmptyCountry(): void
    {
        $this->assertFalse($this->class->isCountryEu(''));
    }

    public function testEuCountry(): void
    {
        $this->assertTrue($this->class->isCountryEu('AB'));
        $this->assertTrue($this->class->isCountryEu('BC'));
        $this->assertTrue($this->class->isCountryEu('XY'));
    }

    public function testNotEuCountry(): void
    {
        $this->assertFalse($this->class->isCountryEu('YY'));
    }

}
