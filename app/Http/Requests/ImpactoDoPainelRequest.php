<?php

namespace App\Http\Requests;

use App\Painel\Intervalo;
use App\Painel\Periodo;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ImpactoDoPainelRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'periodo' => ['sometimes', Rule::enum(Periodo::class)],
            'inicio' => ['required_if:periodo,personalizado', 'date', 'before_or_equal:today'],
            'fim' => ['required_if:periodo,personalizado', 'date', 'after_or_equal:inicio', 'before_or_equal:today'],
            'categoria' => ['nullable', 'string', 'max:100'],
        ];
    }

    public function periodo(): Periodo
    {
        return $this->enum('periodo', Periodo::class) ?? Periodo::TrintaDias;
    }

    public function intervalo(): Intervalo
    {
        return $this->periodo()->intervalo(
            $this->date('inicio')?->toImmutable(),
            $this->date('fim')?->toImmutable(),
        );
    }

    public function categoria(): ?string
    {
        return $this->string('categoria')->trim()->toString() ?: null;
    }
}
