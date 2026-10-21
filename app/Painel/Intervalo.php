<?php

namespace App\Painel;

use Carbon\CarbonImmutable;

final readonly class Intervalo
{
    private const DIAS_ATE_AGRUPAR_POR_SEMANA = 14;

    public function __construct(
        public CarbonImmutable $inicio,
        public CarbonImmutable $fim,
    ) {}

    public static function entre(CarbonImmutable $inicio, CarbonImmutable $fim): self
    {
        return new self($inicio->startOfDay(), $fim->endOfDay());
    }

    public function dias(): int
    {
        return (int) $this->inicio->diffInDays($this->fim->startOfDay()) + 1;
    }

    public function anterior(): self
    {
        return self::entre($this->inicio->subDays($this->dias()), $this->inicio->subDay());
    }

    public function agrupaPorSemana(): bool
    {
        return $this->dias() > self::DIAS_ATE_AGRUPAR_POR_SEMANA;
    }

    /** @return list<self> */
    public function fatias(): array
    {
        $tamanho = $this->agrupaPorSemana() ? 7 : 1;
        $fatias = [];

        for ($inicio = $this->inicio; $inicio->lte($this->fim); $inicio = $inicio->addDays($tamanho)) {
            $fatias[] = self::entre($inicio, $inicio->addDays($tamanho - 1)->min($this->fim));
        }

        return $fatias;
    }
}
