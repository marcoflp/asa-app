import { provideHttpClient } from '@angular/common/http';
import { HttpTestingController, provideHttpClientTesting } from '@angular/common/http/testing';
import { LOCALE_ID } from '@angular/core';
import { ComponentFixture, TestBed } from '@angular/core/testing';
import { registerLocaleData } from '@angular/common';
import localePt from '@angular/common/locales/pt';
import { ConfiguracaoDoPainel } from './configuracao';
import { ImpactoDoPeriodo, PontosDeAtencao } from './modelos';
import { Painel } from './painel';

const configuracao: ConfiguracaoDoPainel = {
  usuario: 'Marco Dias',
  horarioDeAtendimento: 'das 15h às 16h',
  rotas: {
    novaRetirada: '/retiradas/nova',
    novaFamilia: '/beneficiarios/novo',
    retiradas: '/retiradas',
    produtos: '/produtos',
    produtosComEstoqueBaixo: '/produtos?statusEstoque=baixo_estoque',
    cadastrosSemDocumento: '/beneficiarios?documentos=sem_documentos',
  },
  api: {
    atencao: '/api/painel/atencao',
    impacto: '/api/painel/impacto',
    relatorio: '/api/painel/relatorio',
  },
};

const pontos: PontosDeAtencao = {
  atendidasHoje: [{ id: 1, familia: 'Ana Lima', itens: 3, url: '/retiradas/1/editar' }],
  estoqueAcabando: [
    { id: 2, nome: 'Leite', estoque: 3, unidade: 'litro', url: '/produtos/2/editar' },
  ],
  familiasSemVir: [],
  diasSemVir: 60,
  cadastrosSemDocumento: 0,
  produtosUrgentes: ['Fralda', 'Leite'],
};

const impacto: ImpactoDoPeriodo = {
  intervalo: { inicio: '2026-09-21', fim: '2026-10-20', agrupamento: 'semana' },
  indicadores: {
    familias: { valor: 43, variacao: 8 },
    pessoas: { valor: 165, variacao: 9 },
    retiradas: { valor: 69, variacao: -15 },
    itens: { valor: 1474, variacao: null },
  },
  retiradasAoLongoDoTempo: [{ inicio: '2026-09-21', fim: '2026-09-27', total: 14 }],
  itensPorCategoria: [
    { nome: 'Alimento', total: 212 },
    { nome: 'Higiene', total: 141 },
  ],
  produtosMaisRetirados: [{ nome: 'Arroz', categoria: 'Alimento', unidade: 'kg', total: 38 }],
  familiasPorBairro: [{ nome: 'Centro', total: 4, agrupado: false }],
};

describe('Painel', () => {
  let fixture: ComponentFixture<Painel>;
  let http: HttpTestingController;
  let elemento: HTMLElement;

  beforeAll(() => registerLocaleData(localePt));

  beforeEach(async () => {
    TestBed.configureTestingModule({
      providers: [
        provideHttpClient(),
        provideHttpClientTesting(),
        { provide: LOCALE_ID, useValue: 'pt-BR' },
      ],
    });
    http = TestBed.inject(HttpTestingController);
    fixture = TestBed.createComponent(Painel);
    fixture.componentRef.setInput('configuracao', JSON.stringify(configuracao));
    elemento = fixture.nativeElement;
    TestBed.tick();
  });

  afterEach(() => http.verify());

  async function responder(): Promise<void> {
    http.expectOne('/api/painel/atencao').flush(pontos);
    http.expectOne((requisicao) => requisicao.url === '/api/painel/impacto').flush(impacto);
    await fixture.whenStable();
  }

  it('mostra o que fazer hoje e os números do período', async () => {
    await responder();

    expect(elemento.textContent).toContain('Marco');
    expect(elemento.textContent).toContain('Ana Lima');
    expect(elemento.textContent).toContain('3 litros');
    expect(elemento.textContent).toContain('Fralda e Leite.');
    expect(elemento.textContent).toContain('1.474');
    expect(elemento.textContent).toContain('15% a menos que no período anterior');
    expect(elemento.textContent).toContain('Retiradas por semana');
  });

  it('busca de novo com a categoria ao clicar nela, e limpa ao trocar de período', async () => {
    await responder();

    const higiene = [
      ...elemento.querySelectorAll<HTMLButtonElement>('asa-barras-horizontais button'),
    ].find((botao) => botao.textContent?.includes('Higiene'));
    higiene?.click();
    TestBed.tick();

    const comCategoria = http.expectOne((requisicao) => requisicao.url === '/api/painel/impacto');
    expect(comCategoria.request.params.get('categoria')).toBe('Higiene');
    comCategoria.flush(impacto);
    TestBed.tick();

    [...elemento.querySelectorAll<HTMLButtonElement>('asa-seletor-de-periodo button')]
      .find((botao) => botao.textContent?.trim() === '7 dias')
      ?.click();
    TestBed.tick();

    const novoPeriodo = http.expectOne((requisicao) => requisicao.url === '/api/painel/impacto');
    expect(novoPeriodo.request.params.get('periodo')).toBe('7-dias');
    expect(novoPeriodo.request.params.has('categoria')).toBe(false);
    novoPeriodo.flush(impacto);
  });

  it('oferece tentar de novo quando a API falha', async () => {
    http.expectOne('/api/painel/atencao').flush(null, { status: 500, statusText: 'Erro' });
    http.expectOne((requisicao) => requisicao.url === '/api/painel/impacto').flush(impacto);
    await fixture.whenStable();

    expect(elemento.textContent).toContain('Não foi possível carregar os avisos do dia.');
  });
});
