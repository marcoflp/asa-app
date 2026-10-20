<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Produtos com estoque negativo são os que a ASA parou de registrar as entradas.
 * Eles passam a ficar "sem controle de estoque" (estoque nulo), como os demais que não são controlados.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::table('produtos')->where('estoque', '<', 0)->update(['estoque' => null]);
    }

    public function down(): void
    {
        // Não há como saber os valores negativos antigos; eles estavam errados de qualquer forma.
    }
};
