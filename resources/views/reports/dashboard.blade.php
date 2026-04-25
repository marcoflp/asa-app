<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>{{ $titulo }}</title>
    <style>
        body { font-family: sans-serif; font-size: 14px; color: #333; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #3b82f6; padding-bottom: 10px; }
        .header h1 { margin: 0; color: #1e40af; }
        .header p { margin: 5px 0 0; color: #666; }
        
        .summary { display: table; width: 100%; margin-bottom: 30px; }
        .summary-item { display: table-cell; width: 33%; text-align: center; border: 1px solid #e5e7eb; padding: 15px; border-radius: 8px; }
        .summary-item .label { font-size: 12px; color: #6b7280; text-transform: uppercase; }
        .summary-item .value { font-size: 24px; font-weight: bold; margin-top: 5px; }

        .section { margin-bottom: 30px; }
        .section-title { font-size: 18px; font-weight: bold; margin-bottom: 15px; border-left: 4px solid #3b82f6; padding-left: 10px; }
        
        table { width: 100%; border-collapse: collapse; }
        th, td { text-align: left; padding: 10px; border-bottom: 1px solid #e5e7eb; }
        th { background-color: #f9fafb; font-size: 12px; color: #6b7280; text-transform: uppercase; }
        
        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 10px; color: #999; border-top: 1px solid #eee; padding-top: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Relatório ASA</h1>
        <p>{{ $inicio->format('d/m/Y') }} a {{ $fim->format('d/m/Y') }} ({{ ucfirst($periodo) }})</p>
    </div>

    <div class="summary">
        <div class="summary-item">
            <div class="label">Retiradas</div>
            <div class="value">{{ $totalRetiradas }}</div>
        </div>
        <div class="summary-item">
            <div class="label">Beneficiários</div>
            <div class="value">{{ $totalBeneficiarios }}</div>
        </div>
        <div class="summary-item">
            <div class="label">Itens Entregues</div>
            <div class="value">{{ $totalItens }}</div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Produtos mais retirados</div>
        <table>
            <thead>
                <tr>
                    <th>Produto</th>
                    <th>Categoria</th>
                    <th style="text-align: right;">Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse($topProdutos as $tp)
                <tr>
                    <td>{{ $tp->produto->nome }}</td>
                    <td>{{ ucfirst($tp->produto->categoria) }}</td>
                    <td style="text-align: right;">{{ $tp->total }} {{ $tp->produto->unidade }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" style="text-align: center;">Nenhum dado disponível</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Totais Gerais do Sistema</div>
        <p><strong>Total de Beneficiários Cadastrados:</strong> {{ $totalBeneficiariosGeral }}</p>
        <p><strong>Total de Produtos Ativos no Catálogo:</strong> {{ $totalProdutosGeral }}</p>
    </div>

    <div class="footer">
        Gerado em {{ now()->format('d/m/Y H:i') }} - Sistema ASA
    </div>
</body>
</html>
