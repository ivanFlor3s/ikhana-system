<?php

namespace App\Enums;

enum Agreement: string
{
    case CONVENIO_MULTILATERAL = 'convenio_multilateral';

    public function label(): string
    {
        return match($this) {
            self::CONVENIO_MULTILATERAL => 'Convenio Multilateral',
        };
    }

    public static function toArray(): array
    {
        return array_map(
            fn($case) => [
                'value' => $case->value,
                'label' => $case->label()
            ],
            self::cases()
        );
    }
}

