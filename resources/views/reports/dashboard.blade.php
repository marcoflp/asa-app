<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <style>
        @page { margin: 1cm; }
        body { font-family: 'Helvetica', sans-serif; color: #333; line-height: 1.4; }
        
        /* Cabeçalho */
        .header-table { width: 100%; border-bottom: 2px solid #eee; padding-bottom: 20px; margin-bottom: 30px; }
        .logo { font-size: 28px; font-weight: bold; color: #2563eb; }
        .report-type { text-align: right; color: #666; font-size: 12px; }
        
        .title { font-size: 18px; font-weight: bold; margin-bottom: 25px; color: #111; }

        /* Cartões de Resumo */
        .stats-table { width: 100%; border-collapse: separate; border-spacing: 10px 0; margin-bottom: 30px; }
        .card { background: #fdfdfd; border: 1px solid #e5e7eb; border-radius: 8px; padding: 15px; text-align: center; }
        .card-label { font-size: 10px; color: #6b7280; text-transform: uppercase; font-weight: bold; margin-bottom: 5px; }
        .card-value { font-size: 20px; font-weight: bold; color: #111; }

        /* Seções */
        .section-title { font-size: 14px; font-weight: bold; color: #374151; border-bottom: 1px solid #eee; padding-bottom: 5px; margin-bottom: 15px; text-transform: uppercase; }

        /* Categorias com Barras */
        .category-table { width: 100%; margin-bottom: 30px; }
        .cat-name { width: 40%; font-size: 12px; padding: 5px 0; }
        .cat-bar-container { width: 50%; }
        .cat-bar-bg { background: #f3f4f6; border-radius: 4px; height: 12px; width: 100%; }
        .cat-bar-fill { background: #3b82f6; height: 12px; border-radius: 4px; }
        .cat-total { width: 10%; text-align: right; font-size: 11px; color: #666; }

        /* Tabela de Itens */
        .data-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .data-table th { background: #f9fafb; padding: 10px; text-align: left; font-size: 11px; color: #6b7280; border-bottom: 2px solid #eee; }
        .data-table td { padding: 10px; font-size: 12px; border-bottom: 1px solid #f3f4f6; }

        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 9px; color: #9ca3af; border-top: 1px solid #eee; padding-top: 10px; }
    </style>
</head>
<body>
    <table class="header-table">
        <tr>
            <td class="logo">ASA</td>
            <td class="report-type">
                <strong>Relatório</strong><br>
                {{ $inicio->format('d/m/Y') }} — {{ $fim->format('d/m/Y') }}
            </td>
        </tr>
    </table>

    <div class="title">Resumo do Período ({{ ucfirst($periodo) }})</div>

    <table class="stats-table">
        <tr>
            <td class="card">
                <div class="card-label">Retiradas Realizadas</div>
                <div class="card-value">{{ $totalRetiradas }}</div>
            </td>
            <td class="card">
                <div class="card-label">Beneficiários Atendidos</div>
                <div class="card-value">{{ $totalBeneficiarios }}</div>
            </td>
            <td class="card">
                <div class="card-label">Total de Itens</div>
                <div class="card-value">{{ $totalItens }}</div>
            </td>
        </tr>
    </table>

    <div class="section-title">Atendimento por Categoria</div>
    <table class="category-table">
        @foreach($itensPorCategoria as $ic)
        <tr>
            <td class="cat-name">{{ $ic->categoria }}</td>
            <td class="cat-bar-container">
                <div class="cat-bar-bg">
                    @php $percent = $totalItens > 0 ? ($ic->total / $totalItens) * 100 : 0; @endphp
                    <div class="cat-bar-fill" style="width: {{ $percent }}%;"></div>
                </div>
            </td>
            <td class="cat-total">{{ $ic->total }}</td>
        </tr>
        @endforeach
    </table>

    <div class="section-title">Principais Itens Distribuídos</div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Item</th>
                <th>Categoria</th>
                <th style="text-align: right;">Quantidade</th>
            </tr>
        </thead>
        <tbody>
            @foreach($topProdutos as $tp)
            <tr>
                <td style="font-weight: bold;">{{ $tp->produto->nome }}</td>
                <td>{{ $tp->produto->categoria }}</td>
                <td style="text-align: right;">{{ $tp->total }} {{ $tp->produto->unidade }}(s)</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Gerado em {{ now()->format('d/m/Y H:i') }}
    </div>
</body>
</html>
