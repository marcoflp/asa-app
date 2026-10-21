<?php

namespace App\Painel;

use Carbon\CarbonImmutable;

enum Periodo: string
{
    case Hoje = 'hoje';
    case SeteDias = '7-dias';
    case TrintaDias = '30-dias';
    case TresMeses = '3-meses';
    case SeisMeses = '6-meses';
    case Personalizado = 'personalizado';

    public function intervalo(?CarbonImmutable $inicio = null, ?CarbonImmutable $fim = null): Intervalo
    {
        $hoje = CarbonImmutable::today();

        return match ($this) {
            self::Hoje => Intervalo::entre($hoje, $hoje),
            self::SeteDias => Intervalo::entre($hoje->subDays(6), $hoje),
            self::TrintaDias => Intervalo::entre($hoje->subDays(29), $hoje),
            self::TresMeses => Intervalo::entre($hoje->subMonths(3)->addDay(), $hoje),
            self::SeisMeses => Intervalo::entre($hoje->subMonths(6)->addDay(), $hoje),
            self::Personalizado => Intervalo::entre($inicio ?? $hoje, $fim ?? $hoje),
        };
    }

    public function rotulo(): string
    {
        return match ($this) {
            self::Hoje => 'Hoje',
            self::SeteDias => 'Últimos 7 dias',
            self::TrintaDias => 'Últimos 30 dias',
            self::TresMeses => 'Últimos 3 meses',
            self::SeisMeses => 'Últimos 6 meses',
            self::Personalizado => 'Período personalizado',
        };
    }
}
