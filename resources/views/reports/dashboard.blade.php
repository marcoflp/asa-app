<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Relatório ASA</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');
        
        @page { margin: 30px; }
        body { 
            font-family: 'Inter', Helvetica, Arial, sans-serif; 
            color: #1e293b; 
            line-height: 1.5;
            background-color: #ffffff;
            margin: 0;
            padding: 0;
        }

        /* Top Header Container */
        .header-section {
            background-color: #ffffff;
            padding: 0 0 20px 0;
            border-bottom: 2px solid #1a2f26;
            margin-bottom: 30px;
        }

        .header-table { width: 100%; border-collapse: collapse; }
        .header-table td { vertical-align: middle; }
        .logo-img { max-height: 60px; width: auto; }
        .report-meta { text-align: right; }
        .report-title-top { font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #1a2f26; margin-bottom: 4px; }
        .report-date { font-size: 14px; font-weight: 500; color: #64748b; }

        /* Section Titles */
        .section-header { margin-bottom: 25px; padding-left: 5px; }
        .main-title { font-size: 20px; font-weight: 700; color: #1a2f26; margin: 0; letter-spacing: -0.5px; }
        .sub-title { font-size: 13px; color: #64748b; margin-top: 4px; }

        /* KPI Cards */
        .kpi-container { width: 100%; margin-bottom: 35px; text-align: center; }
        .kpi-card {
            width: 31%;
            display: inline-block;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 15px;
            box-sizing: border-box;
            text-align: left;
            margin-right: 2%;
            vertical-align: top;
        }
        .kpi-card.last { margin-right: 0; }
        .kpi-label { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #64748b; margin-bottom: 6px; }
        .kpi-value { font-size: 28px; font-weight: 800; color: #1a2f26; letter-spacing: -1px; line-height: 1; }

        /* Two columns */
        .row { width: 100%; margin-bottom: 30px; clear: both; }
        .col-left { width: 48%; float: left; }
        .col-right { width: 48%; float: right; }

        /* Box Title */
        .box-title { font-size: 14px; font-weight: 700; color: #1a2f26; border-bottom: 2px solid #f1f5f9; padding-bottom: 8px; margin-bottom: 15px; }

        /* Bars Table */
        .bar-table { width: 100%; border-collapse: collapse; }
        .bar-table td { padding: 6px 0; border-bottom: 1px dashed #f1f5f9; }
        .bar-table tr:last-child td { border-bottom: none; }
        .cat-name { width: 35%; font-size: 12px; font-weight: 500; color: #334155; }
        .cat-bar-cell { width: 50%; padding: 0 15px !important; vertical-align: middle; }
        .cat-bar-bg { background-color: #f1f5f9; border-radius: 4px; height: 6px; width: 100%; overflow: hidden; }
        .cat-bar-fill { background-color: #d6a838; height: 6px; border-radius: 4px; }
        .cat-val { width: 15%; text-align: right; font-size: 12px; font-weight: 600; color: #1a2f26; }

        /* Data Table */
        .data-table { width: 100%; border-collapse: collapse; }
        .data-table th { background-color: #f8fafc; padding: 10px 12px; text-align: left; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #64748b; border-bottom: 2px solid #e2e8f0; }
        .data-table td { padding: 10px 12px; font-size: 12px; color: #334155; border-bottom: 1px solid #f1f5f9; }
        .data-table tr:last-child td { border-bottom: 2px solid #e2e8f0; }
        .data-table td.qty { text-align: right; font-weight: 600; color: #1a2f26; }

        /* Footer */
        .footer {
            position: fixed;
            bottom: 0px;
            left: 0;
            width: 100%;
            padding-top: 15px;
            border-top: 1px solid #e2e8f0;
            font-size: 9px;
            color: #94a3b8;
            text-align: center;
        }
        
        .footer-logo { font-weight: 700; color: #64748b; }
        
        .page-break { page-break-after: always; }
    </style>
</head>
<body>
    <div class="header-section">
        <table class="header-table">
            <tr>
                <td style="width: 50%;">
                    @if(file_exists(public_path('logoescrita.jpg')))
                        <img src="{{ public_path('logoescrita.jpg') }}" class="logo-img" alt="Logo ASA">
                    @else
                        <h2 style="color: #1a2f26; margin: 0;">ASA</h2>
                    @endif
                </td>
                <td class="report-meta" style="width: 50%;">
                    <div class="report-title-top">Relatório ASA</div>
                    <div class="report-date">{{ $inicio->format('d/m/Y') }} — {{ $fim->format('d/m/Y') }}</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="section-header">
        <h1 class="main-title">Resumo do Período ({{ ucfirst($periodo) }})</h1>
        <div class="sub-title">Visão consolidada de movimentações, beneficiários atendidos e entregas realizadas.</div>
    </div>

    <div class="kpi-container">
        <div class="kpi-card">
            <div class="kpi-label">Retiradas Realizadas</div>
            <div class="kpi-value">{{ $totalRetiradas }}</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-label">Beneficiários Atendidos</div>
            <div class="kpi-value">{{ $totalBeneficiarios }}</div>
        </div>
        <div class="kpi-card last">
            <div class="kpi-label">Total de Itens</div>
            <div class="kpi-value">{{ $totalItens }}</div>
        </div>
    </div>

    <div class="row">
        <div class="col-left">
            <div class="box-title">Atendimento por Categoria</div>
            <table class="bar-table">
                @forelse($itensPorCategoria as $ic)
                <tr>
                    <td class="cat-name">{{ $ic->categoria }}</td>
                    <td class="cat-bar-cell">
                        <div class="cat-bar-bg">
                            @php $percent = $totalItens > 0 ? ($ic->total / $totalItens) * 100 : 0; @endphp
                            <div class="cat-bar-fill" style="width: {{ $percent }}%;"></div>
                        </div>
                    </td>
                    <td class="cat-val">{{ $ic->total }}</td>
                </tr>
                @empty
                <tr><td colspan="3" style="font-size: 12px; color: #94a3b8; padding: 10px 0;">Nenhuma categoria registrada.</td></tr>
                @endforelse
            </table>
        </div>

        <div class="col-right">
            <div class="box-title">Top Produtos Distribuídos</div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Produto</th>
                        <th style="text-align: right;">Qtd</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($topProdutos as $tp)
                    <tr>
                        <td style="font-weight: 500;">
                            {{ $tp->produto->nome }}
                            <div style="font-size: 10px; color: #94a3b8; font-weight: normal; margin-top: 2px;">{{ $tp->produto->categoria }}</div>
                        </td>
                        <td class="qty">{{ $tp->total }} <span style="font-size: 9px; font-weight: normal; color: #64748b;">{{ $tp->produto->unidade }}</span></td>
                    </tr>
                    @empty
                    <tr><td colspan="2" style="font-size: 12px; color: #94a3b8; text-align: center; padding: 15px;">Nenhum produto distribuído.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="footer">
        Gerado pelo sistema <span class="footer-logo">ASA</span> em {{ now()->format('d/m/Y \à\s H:i') }} &nbsp;|&nbsp; Documento interno confidencial
    </div>
</body>
</html>
