export interface ConfiguracaoDoPainel {
  readonly horarioDeAtendimento: string;
  readonly rotas: {
    readonly novaRetirada: string;
    readonly novaFamilia: string;
    readonly retiradas: string;
    readonly produtos: string;
    readonly produtosComEstoqueBaixo: string;
    readonly cadastrosSemDocumento: string;
  };
  readonly api: {
    readonly atencao: string;
    readonly impacto: string;
    readonly relatorio: string;
  };
}

export function lerConfiguracao(json: string): ConfiguracaoDoPainel {
  return JSON.parse(json) as ConfiguracaoDoPainel;
}
