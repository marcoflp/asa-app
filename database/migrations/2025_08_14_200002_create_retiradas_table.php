<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('retiradas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('beneficiario_id')->constrained()->cascadeOnDelete();
            $table->date('data');
            $table->text('observacoes')->nullable();
            $table->timestamps();
        });

        Schema::create('retirada_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('retirada_id')->constrained()->cascadeOnDelete();
            $table->foreignId('produto_id')->constrained()->restrictOnDelete();
            $table->unsignedInteger('quantidade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('retirada_items');
        Schema::dropIfExists('retiradas');
    }
};
