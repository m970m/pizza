<?php
declare(strict_types=1);

namespace App\Enums;

enum ProductType: string
{
    case PIZZA = 'pizza';
    case DRINK = 'drink';

    public static function values(): array
    {
        return array_map(fn(self $case) => $case->value, self::cases());
    }
}
