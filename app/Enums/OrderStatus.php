<?php
declare(strict_types=1);

namespace App\Enums;

enum OrderStatus: string
{
    case PENDING = 'pending';
    case SHIPPED = 'shipped';
    case DELIVERED = 'delivered';
    case CANCELLED = 'cancelled';

    public static function values(): array
    {
        return array_map(fn(self $case) => $case->value, self::cases());
    }
}
