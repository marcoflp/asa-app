<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RetiradaItem extends Model
{
    public $timestamps = false;

    protected $fillable = ['retirada_id', 'produto_id', 'quantidade'];

    protected static function booted()
    {
        static::created(function (RetiradaItem $item) {
            $produto = $item->produto;
            if ($produto->estoque !== null) {
                $produto->decrement('estoque', $item->quantidade);
            }
        });

        static::deleted(function (RetiradaItem $item) {
            $produto = $item->produto;
            if ($produto->estoque !== null) {
                $produto->increment('estoque', $item->quantidade);
            }
        });
    }

    public function produto(): BelongsTo
    {
        return $this->belongsTo(Produto::class);
    }

    public function retirada(): BelongsTo
    {
        return $this->belongsTo(Retirada::class);
    }
}
