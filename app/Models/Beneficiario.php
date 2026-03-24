<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Beneficiario extends Model
{
    protected $fillable = [
        'nome', 'telefone', 'rua', 'numero', 'bairro', 'cidade', 'cep',
        'rg', 'cpf', 'num_pessoas_familia', 'filhos',
        'inscrito_programa_governo', 'programa_governo',
        'recebe_estudo_biblico', 'instrutor_biblico', 'observacoes',
    ];

    protected $casts = [
        'filhos' => 'array',
        'inscrito_programa_governo' => 'boolean',
        'recebe_estudo_biblico' => 'boolean',
    ];

    public function getNumFilhosAttribute(): int
    {
        return count($this->filhos ?? []);
    }

    public function retiradas(): HasMany
    {
        return $this->hasMany(Retirada::class);
    }
}
