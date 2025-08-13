<?php

namespace App\Enums;

enum RoleEnum: string
{
    case ADMIN = 'admin';
    case PROVIDER = 'provider';
    case CUSTOMER = 'customer';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function permissions(): array
    {
        return match($this) {
            self::ADMIN => [
                'viewAny', 'view', 'create', 'update', 'delete',
                'viewReports', 'manageUsers', 'manageServices'
            ],
            self::PROVIDER => [
                'manageOwnServices', 'manageAvailability',
                'manageBookings', 'viewOwnReports'
            ],
            self::CUSTOMER => [
                'viewServices', 'createBookings',
                'viewOwnBookings', 'cancelOwnBookings'
            ]
        };
    }
}