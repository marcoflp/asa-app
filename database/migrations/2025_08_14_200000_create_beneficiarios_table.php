<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('beneficiarios', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('telefone')->nullable();
            $table->string('rua')->nullable();
            $table->string('numero')->nullable();
            $table->string('bairro')->nullable();
            $table->string('cidade')->default('Passo Fundo');
            $table->string('cep', 9)->nullable();
            $table->string('rg', 20)->nullable();
            $table->string('cpf', 14)->nullable()->unique();
            $table->unsignedTinyInteger('num_pessoas_familia')->default(1);
            $table->json('filhos')->nullable(); // [{idade: 5}, {idade: 8}]
            $table->boolean('inscrito_programa_governo')->default(false);
            $table->string('programa_governo')->nullable();
            $table->boolean('recebe_estudo_biblico')->default(false);
            $table->string('instrutor_biblico')->nullable();
            $table->text('observacoes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('beneficiarios');
    }
};
