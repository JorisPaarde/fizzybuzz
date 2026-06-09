<?php

namespace App\Enums;

enum PurchaseSize: string
{
    case Small = 'small';
    case Medium = 'medium';
    case Large = 'large';

    public function label(): string
    {
        return match ($this) {
            self::Small => 'Klein (tot €5.000/maand)',
            self::Medium => 'Middel (€5.000 – €20.000/maand)',
            self::Large => 'Groot (meer dan €20.000/maand)',
        };
    }

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        $options = [];

        foreach (self::cases() as $case) {
            $options[$case->value] = $case->label();
        }

        return $options;
    }
}
