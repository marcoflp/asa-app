<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Produto extends Model
{
    protected $fillable = ['nome', 'categoria', 'unidade', 'estoque', 'descricao', 'ativo'];

    protected $casts = ['ativo' => 'boolean'];

    public function scopeAtivo($query)
    {
        return $query->where('ativo', true);
    }

    public function retiradaItems(): HasMany
    {
        return $this->hasMany(RetiradaItem::class);
    }
}
