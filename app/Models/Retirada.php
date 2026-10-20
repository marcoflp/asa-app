<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Retirada extends Model
{
    protected $fillable = ['beneficiario_id', 'data', 'observacoes'];

    protected $casts = ['data' => 'date'];

    protected static function booted()
    {
        // Apaga os itens um a um para devolver as quantidades ao estoque (o cascade do banco não faz isso)
        static::deleting(function (Retirada $retirada) {
            $retirada->items()->get()->each->delete();
        });
    }

    public function beneficiario(): BelongsTo
    {
        return $this->belongsTo(Beneficiario::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(RetiradaItem::class);
    }
}
