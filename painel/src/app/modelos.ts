export type Periodo = 'hoje' | '7-dias' | '30-dias' | '3-meses' | '6-meses' | 'personalizado';

export type Agrupamento = 'dia' | 'semana';

export type NomeDoIndicador = 'familias' | 'pessoas' | 'retiradas' | 'itens';

export interface Indicador {
  readonly valor: number;
  readonly variacao: number | null;
}

export interface FatiaDoPeriodo {
  readonly inicio: string;
  readonly fim: string;
  readonly total: number;
}

export interface TotalPorNome {
  readonly nome: string;
  readonly total: number;
}

export interface ProdutoRetirado extends TotalPorNome {
  readonly categoria: string;
  readonly unidade: string;
}

export interface FamiliasDoBairro extends TotalPorNome {
  readonly agrupado: boolean;
}

export interface ImpactoDoPeriodo {
  readonly intervalo: {
    readonly inicio: string;
    readonly fim: string;
    readonly agrupamento: Agrupamento;
  };
  readonly indicadores: Readonly<Record<NomeDoIndicador, Indicador>>;
  readonly retiradasAoLongoDoTempo: readonly FatiaDoPeriodo[];
  readonly itensPorCategoria: readonly TotalPorNome[];
  readonly produtosMaisRetirados: readonly ProdutoRetirado[];
  readonly familiasPorBairro: readonly FamiliasDoBairro[];
}

export interface RetiradaDeHoje {
  readonly id: number;
  readonly familia: string | null;
  readonly itens: number;
  readonly url: string;
}

export interface ProdutoAcabando {
  readonly id: number;
  readonly nome: string;
  readonly estoque: number;
  readonly unidade: string;
  readonly url: string;
}

export interface FamiliaSemVir {
  readonly id: number;
  readonly nome: string;
  readonly diasSemVir: number;
  readonly url: string;
}

export interface PontosDeAtencao {
  readonly atendidasHoje: readonly RetiradaDeHoje[];
  readonly estoqueAcabando: readonly ProdutoAcabando[];
  readonly familiasSemVir: readonly FamiliaSemVir[];
  readonly diasSemVir: number;
  readonly cadastrosSemDocumento: number;
  readonly produtosUrgentes: readonly string[];
}

export interface FiltroDoImpacto {
  readonly periodo: Periodo;
  readonly inicio: string;
  readonly fim: string;
  readonly categoria: string | null;
}

export interface BarraHorizontal {
  readonly nome: string;
  readonly valor: number;
  readonly complemento: string;
  readonly secundaria: boolean;
}

export interface ColunaDoGrafico {
  readonly rotulo: string;
  readonly detalhe: string;
  readonly valor: number;
}
