<?php

namespace App\Enums;

enum TaxStatus: string
{
    case RESPONSABLE_INSCRIPTO = '1';
    case RESPONSABLE_NO_INSCRIPTO = '2';
    case NO_RESPONSABLE = '3';
    case SUJETO_EXENTO = '4';
    case CONSUMIDOR_FINAL = '5';
    case RESPONSABLE_MONOTRIBUTO = '6';
    case SUJETO_NO_CATEGORIZADO = '7';
    case PROVEEDOR_EXTERIOR = '8';
    case CLIENTE_EXTERIOR = '9';
    case IVA_LIBERADO = '10';
    case RESPONSABLE_INSCRIPTO_AGENTE = '11';
    case PEQUENO_CONTRIBUYENTE_EVENTUAL = '12';
    case MONOTRIBUTISTA_SOCIAL = '13';
    case PEQUENO_CONTRIBUYENTE_EVENTUAL_SOCIAL = '14';

    public function label(): string
    {
        return match($this) {
            self::RESPONSABLE_INSCRIPTO => 'IVA Responsable Inscripto',
            self::RESPONSABLE_NO_INSCRIPTO => 'IVA Responsable no Inscripto',
            self::NO_RESPONSABLE => 'IVA no Responsable',
            self::SUJETO_EXENTO => 'IVA Sujeto Exento',
            self::CONSUMIDOR_FINAL => 'Consumidor Final',
            self::RESPONSABLE_MONOTRIBUTO => 'Responsable Monotributo',
            self::SUJETO_NO_CATEGORIZADO => 'Sujeto no Categorizado',
            self::PROVEEDOR_EXTERIOR => 'Proveedor del Exterior',
            self::CLIENTE_EXTERIOR => 'Cliente del Exterior',
            self::IVA_LIBERADO => 'IVA Liberado – Ley Nº 19.640',
            self::RESPONSABLE_INSCRIPTO_AGENTE => 'IVA Responsable Inscripto – Agente de Percepción',
            self::PEQUENO_CONTRIBUYENTE_EVENTUAL => 'Pequeño Contribuyente Eventual',
            self::MONOTRIBUTISTA_SOCIAL => 'Monotributista Social',
            self::PEQUENO_CONTRIBUYENTE_EVENTUAL_SOCIAL => 'Pequeño Contribuyente Eventual Social',
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

