<?php

declare(strict_types = 1);

namespace ComissionCalculator\Views;

interface ViewInterface
{
    public static function print(array $rows): string;
}
