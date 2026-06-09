<?php

namespace App\Enums;

enum AddedVia: string
{
    case Upload = 'upload';
    case Manual = 'manual';

    public function label(): string
    {
        return match ($this) {
            self::Upload => 'Via upload',
            self::Manual => 'Handmatig toegevoegd',
        };
    }
}
