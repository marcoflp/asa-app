<?php

use Illuminate\Support\Facades\Route;

Route::get('/offline', fn() => response()->file(public_path('offline.html')))->name('offline');

Route::get('/', function () {
    // A coordenação escolhe o que aparece marcando o produto como urgente
    $produtosEmFalta = \App\Models\Produto::ativo()
        ->where('urgente', true)
        ->orderBy('categoria')
        ->orderBy('nome')
        ->limit(12)
        ->get();

    return view('welcome', compact('produtosEmFalta'));
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', \App\Http\Controllers\PainelInicialController::class)->name('dashboard');

    Route::prefix('api/painel')
        ->name('api.painel.')
        ->controller(\App\Http\Controllers\Api\PainelController::class)
        ->group(function () {
            Route::get('atencao', 'atencao')->name('atencao');
            Route::get('impacto', 'impacto')->name('impacto');
            Route::get('relatorio', 'relatorio')->name('relatorio');
        });

    Route::get('usuarios', \App\Livewire\Usuarios\Index::class)->name('usuarios.index');
    Route::get('usuarios/novo', \App\Livewire\Usuarios\Form::class)->name('usuarios.create');
    Route::get('usuarios/{user}/editar', \App\Livewire\Usuarios\Form::class)->name('usuarios.edit');

    Route::get('beneficiarios', \App\Livewire\Beneficiarios\Index::class)->name('beneficiarios.index');
    Route::get('beneficiarios/novo', \App\Livewire\Beneficiarios\Form::class)->name('beneficiarios.create');
    Route::get('beneficiarios/{beneficiario}/editar', \App\Livewire\Beneficiarios\Form::class)->name('beneficiarios.edit');

    Route::get('retiradas', \App\Livewire\Retiradas\Index::class)->name('retiradas.index');
    Route::get('retiradas/nova', \App\Livewire\Retiradas\Form::class)->name('retiradas.create');
    Route::get('retiradas/{retirada}/editar', \App\Livewire\Retiradas\Form::class)->name('retiradas.edit');

    Route::get('produtos', \App\Livewire\Produtos\Index::class)->name('produtos.index');
    Route::get('produtos/novo', \App\Livewire\Produtos\Form::class)->name('produtos.create');
    Route::get('produtos/{produto}/editar', \App\Livewire\Produtos\Form::class)->name('produtos.edit');
});

require __DIR__.'/settings.php';
