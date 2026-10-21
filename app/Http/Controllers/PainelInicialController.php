<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class PainelInicialController extends Controller
{
    private const HORARIO_DE_ATENDIMENTO = 'das 15h às 16h';

    public function __invoke(): View
    {
        return view('painel', [
            'configuracao' => [
                'horarioDeAtendimento' => self::HORARIO_DE_ATENDIMENTO,
                'rotas' => [
                    'novaRetirada' => route('retiradas.create'),
                    'novaFamilia' => route('beneficiarios.create'),
                    'retiradas' => route('retiradas.index'),
                    'produtos' => route('produtos.index'),
                    'produtosComEstoqueBaixo' => route('produtos.index', ['statusEstoque' => 'baixo_estoque']),
                    'cadastrosSemDocumento' => route('beneficiarios.index', ['documentos' => 'sem_documentos']),
                ],
                'api' => [
                    'atencao' => route('api.painel.atencao'),
                    'impacto' => route('api.painel.impacto'),
                    'relatorio' => route('api.painel.relatorio'),
                ],
            ],
        ]);
    }
}
