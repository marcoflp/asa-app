import { DatePipe } from '@angular/common';
import { httpResource } from '@angular/common/http';
import {
  ChangeDetectionStrategy,
  Component,
  computed,
  effect,
  input,
  linkedSignal,
  signal,
} from '@angular/core';
import { AtendidasHoje } from './atencao/atendidas-hoje';
import { PrecisaDeAtencao } from './atencao/precisa-de-atencao';
import { Cabecalho } from './cabecalho/cabecalho';
import { Icone, NomeDoIcone } from './compartilhado/icone';
import { lerConfiguracao } from './configuracao';
import {
  colunaDaFatia,
  dataDaApi,
  filtroCompleto,
  parametrosDoFiltro,
  unidadeNoPlural,
} from './formatacao';
import { BarrasHorizontais } from './impacto/barras-horizontais';
import { GraficoDeColunas } from './impacto/grafico-de-colunas';
import { IndicadorDoPeriodo } from './impacto/indicador';
import { SeletorDePeriodo } from './impacto/seletor-de-periodo';
import {
  BarraHorizontal,
  FiltroDoImpacto,
  ImpactoDoPeriodo,
  NomeDoIndicador,
  Periodo,
  PontosDeAtencao,
} from './modelos';
import { enderecoComPeriodo, periodoDoEndereco } from './periodos';

const INDICADORES: readonly {
  readonly nome: NomeDoIndicador;
  readonly rotulo: string;
  readonly icone: NomeDoIcone;
}[] = [
  { nome: 'familias', rotulo: 'Famílias atendidas', icone: 'casa' },
  { nome: 'pessoas', rotulo: 'Pessoas alcançadas', icone: 'pessoas' },
  { nome: 'retiradas', rotulo: 'Retiradas', icone: 'prancheta' },
  { nome: 'itens', rotulo: 'Itens entregues', icone: 'pacote' },
];

@Component({
  selector: 'asa-painel-completo',
  changeDetection: ChangeDetectionStrategy.OnPush,
  imports: [
    AtendidasHoje,
    BarrasHorizontais,
    Cabecalho,
    DatePipe,
    GraficoDeColunas,
    Icone,
    IndicadorDoPeriodo,
    PrecisaDeAtencao,
    SeletorDePeriodo,
  ],
  templateUrl: './painel.html',
  styleUrl: './painel.css',
})
export class Painel {
  readonly configuracao = input.required({ transform: lerConfiguracao });

  protected readonly periodo = signal<Periodo>(periodoDoEndereco(location.href));
  protected readonly inicio = signal('');
  protected readonly fim = signal('');
  protected readonly categoria = signal<string | null>(null);

  private readonly filtro = computed<FiltroDoImpacto>(() => ({
    periodo: this.periodo(),
    inicio: this.inicio(),
    fim: this.fim(),
    categoria: this.categoria(),
  }));

  protected readonly atencao = httpResource<PontosDeAtencao>(() => this.configuracao().api.atencao);

  protected readonly impacto = httpResource<ImpactoDoPeriodo>(() =>
    filtroCompleto(this.filtro())
      ? { url: this.configuracao().api.impacto, params: parametrosDoFiltro(this.filtro()) }
      : undefined,
  );

  protected readonly impactoExibido = linkedSignal<
    ImpactoDoPeriodo | undefined,
    ImpactoDoPeriodo | undefined
  >({
    source: () => (this.impacto.hasValue() ? this.impacto.value() : undefined),
    computation: (atual, anterior) => atual ?? anterior?.value,
  });

  protected readonly indicadores = INDICADORES;

  protected readonly referencia = computed(() =>
    this.periodo() === 'hoje' ? 'dia anterior' : 'período anterior',
  );

  protected readonly inicioDoPeriodo = computed(() => this.dataDoIntervalo('inicio'));
  protected readonly fimDoPeriodo = computed(() => this.dataDoIntervalo('fim'));

  protected readonly tituloDoGrafico = computed(() =>
    this.impactoExibido()?.intervalo.agrupamento === 'semana'
      ? 'Retiradas por semana'
      : 'Retiradas por dia',
  );

  protected readonly colunas = computed(() => {
    const impacto = this.impactoExibido();
    return impacto
      ? impacto.retiradasAoLongoDoTempo.map((fatia) =>
          colunaDaFatia(fatia, impacto.intervalo.agrupamento),
        )
      : [];
  });

  protected readonly categorias = computed<readonly BarraHorizontal[]>(() =>
    (this.impactoExibido()?.itensPorCategoria ?? []).map((categoria) => ({
      nome: categoria.nome,
      valor: categoria.total,
      complemento: '',
      secundaria: this.categoria() !== null && this.categoria() !== categoria.nome,
    })),
  );

  protected readonly produtos = computed<readonly BarraHorizontal[]>(() =>
    (this.impactoExibido()?.produtosMaisRetirados ?? []).map((produto) => ({
      nome: produto.nome,
      valor: produto.total,
      complemento: unidadeNoPlural(produto.total, produto.unidade),
      secundaria: false,
    })),
  );

  protected readonly bairros = computed<readonly BarraHorizontal[]>(() =>
    (this.impactoExibido()?.familiasPorBairro ?? []).map((bairro) => ({
      nome: bairro.nome,
      valor: bairro.total,
      complemento: bairro.total === 1 ? 'família' : 'famílias',
      secundaria: bairro.agrupado,
    })),
  );

  protected readonly enderecoDoRelatorio = computed(() => {
    const parametros = new URLSearchParams(
      parametrosDoFiltro({ ...this.filtro(), categoria: null }),
    );
    return `${this.configuracao().api.relatorio}?${parametros}`;
  });

  constructor() {
    effect(() =>
      history.replaceState(history.state, '', enderecoComPeriodo(location.href, this.periodo())),
    );
  }

  protected escolherPeriodo(periodo: Periodo): void {
    this.periodo.set(periodo);
    this.categoria.set(null);
  }

  protected alternarCategoria(nome: string): void {
    this.categoria.update((atual) => (atual === nome ? null : nome));
  }

  private dataDoIntervalo(limite: 'inicio' | 'fim'): Date | null {
    const intervalo = this.impactoExibido()?.intervalo;
    return intervalo ? dataDaApi(intervalo[limite]) : null;
  }
}
