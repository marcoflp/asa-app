<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <style>
        @page { margin: 40px; }
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 13px; color: #1a1a1a; line-height: 1.5; background: #fff; }
        .header { margin-bottom: 30px; overflow: hidden; border-bottom: 2px solid #f3f4f6; padding-bottom: 20px; }
        .logo { float: left; font-size: 32px; font-weight: 800; color: #2563eb; letter-spacing: -2px; }
        .periodo { float: right; text-align: right; color: #6b7280; margin-top: 10px; }
        
        .title { font-size: 22px; font-weight: 700; clear: both; padding-top: 15px; color: #111827; }
        
        .stats-grid { display: block; margin: 20px 0; }
        .card { 
            display: inline-block; 
            width: 30%; 
            background: #f9fafb; 
            border: 1px solid #f3f4f6; 
            border-radius: 12px; 
            padding: 15px; 
            margin-right: 2%;
            text-align: center;
        }
        .card-label { font-size: 10px; font-weight: 700; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 5px; }
        .card-value { font-size: 24px; font-weight: 800; color: #2563eb; }
        
        .section { margin-top: 40px; }
        .section-header { font-size: 16px; font-weight: 700; margin-bottom: 15px; color: #111827; border-bottom: 2px solid #f3f4f6; padding-bottom: 8px; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { text-align: left; padding: 12px; background: #f9fafb; font-size: 11px; font-weight: 700; color: #6b7280; text-transform: uppercase; border-bottom: 1px solid #e5e7eb; }
        td { padding: 12px; border-bottom: 1px solid #f3f4f6; color: #374151; font-size: 12px; }
        
        .category-row { margin-bottom: 15px; }
        .category-name { font-weight: 600; margin-bottom: 4px; display: block; }
        .progress-container { width: 100%; background: #f3f4f6; height: 10px; border-radius: 5px; }
        .progress-bar { background: #3b82f6; height: 10px; border-radius: 5px; }
        
        .footer { position: fixed; bottom: -10px; width: 100%; text-align: center; font-size: 10px; color: #9ca3af; border-top: 1px solid #f3f4f6; padding-top: 15px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">ASA</div>
        <div class="periodo">
            Relatório de Assistência Social<br>
            <strong>{{ $inicio->format('d/m/Y') }} — {{ $fim->format('d/m/Y') }}</strong>
        </div>
    </div>

    <div class="title">Resumo do Período ({{ ucfirst($periodo) }})</div>

    <div class="stats-grid">
        <div class="card">
            <div class="card-label">Retiradas Realizadas</div>
            <div class="card-value">{{ $totalRetiradas }}</div>
        </div>
        <div class="card">
            <div class="card-label">Beneficiários Atendidos</div>
            <div class="card-value">{{ $totalBeneficiarios }}</div>
        </div>
        <div class="card" style="margin-right: 0;">
            <div class="card-label">Total de Itens</div>
            <div class="card-value">{{ $totalItens }}</div>
        </div>
    </div>

    <div class="section">
        <div class="section-header">Atendimento por Categoria</div>
        <div style="width: 100%;">
            @foreach($itensPorCategoria as $ic)
            <div class="category-row">
                <span class="category-name">{{ $ic->categoria }} <span style="float: right; font-weight: normal; color: #6b7280;">{{ $ic->total }} itens</span></span>
                <div class="progress-container">
                    @php $percent = $totalItens > 0 ? ($ic->total / $totalItens) * 100 : 0; @endphp
                    <div class="progress-bar" style="width: {{ $percent }}%;"></div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <div class="section">
        <div class="section-header">Principais Itens Distribuídos</div>
        <table>
            <thead>
                <tr>
                    <th>Produto</th>
                    <th>Categoria</th>
                    <th style="text-align: right;">Quantidade</th>
                </tr>
            </thead>
            <tbody>
                @foreach($topProdutos as $tp)
                <tr>
                    <td style="font-weight: 600;">{{ $tp->produto->nome }}</td>
                    <td>{{ $tp->produto->categoria }}</td>
                    <td style="text-align: right; font-weight: 700;">{{ $tp->total }} {{ $tp->produto->unidade }}(s)</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="footer">
        Gerado automaticamente pelo Sistema ASA em {{ now()->format('d/m/Y H:i') }}
    </div>
</body>
</html>
