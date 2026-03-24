<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Retirada extends Model
{
    protected $fillable = ['beneficiario_id', 'data', 'observacoes'];

    protected $casts = ['data' => 'date'];

    public function beneficiario(): BelongsTo
    {
        return $this->belongsTo(Beneficiario::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(RetiradaItem::class);
    }
}
