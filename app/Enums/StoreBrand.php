<?php

namespace App\Enums;

enum StoreBrand: string
{
    case TESCO = 'tesco';
    case SAINSBURYS = 'sainsburys';
    case BOOTS = 'boots';
    case COOP = 'coop';

    public function label(): string
    {
        return match ($this) {
            self::TESCO => 'Tesco',
            self::SAINSBURYS => "Sainsbury's",
            self::BOOTS => 'Boots',
            self::COOP => 'Co-op',
        };
    }
}
