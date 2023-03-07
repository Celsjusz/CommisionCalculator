<?php

declare(strict_types = 1);

namespace ComissionCalculator\Views;

class SimpleView implements ViewInterface
{
    public static function print(array $rows): string
    {
        return implode(PHP_EOL, $rows);
    }
}
