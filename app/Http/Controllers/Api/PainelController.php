<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ImpactoDoPainelRequest;
use App\Painel\ImpactoDoPeriodo;
use App\Painel\PontosDeAtencao;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PainelController extends Controller
{
    public function atencao(PontosDeAtencao $pontosDeAtencao): JsonResponse
    {
        return response()->json($pontosDeAtencao->gerar());
    }

    public function impacto(ImpactoDoPainelRequest $request, ImpactoDoPeriodo $impacto): JsonResponse
    {
        return response()->json($impacto->gerar($request->intervalo(), $request->categoria()));
    }

    public function relatorio(ImpactoDoPainelRequest $request, ImpactoDoPeriodo $impacto): StreamedResponse
    {
        $intervalo = $request->intervalo();
        $dados = $impacto->gerar($intervalo);

        $pdf = Pdf::loadView('reports.dashboard', [
            'titulo' => 'Relatório de Atividades - '.$request->periodo()->rotulo(),
            'periodo' => $request->periodo()->rotulo(),
            'inicio' => $intervalo->inicio,
            'fim' => $intervalo->fim,
            'indicadores' => $dados['indicadores'],
            'itensPorCategoria' => $dados['itensPorCategoria'],
            'produtosMaisRetirados' => $dados['produtosMaisRetirados'],
        ]);

        return response()->streamDownload(
            fn () => print ($pdf->output()),
            "relatorio-asa-{$intervalo->inicio->toDateString()}-a-{$intervalo->fim->toDateString()}.pdf",
        );
    }
}
