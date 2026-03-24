<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RetiradaItem extends Model
{
    public $timestamps = false;

    protected $fillable = ['retirada_id', 'produto_id', 'quantidade'];

    public function produto(): BelongsTo
    {
        return $this->belongsTo(Produto::class);
    }

    public function retirada(): BelongsTo
    {
        return $this->belongsTo(Retirada::class);
    }
}
