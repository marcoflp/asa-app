import { Agrupamento, ColunaDoGrafico, FatiaDoPeriodo, FiltroDoImpacto } from './modelos';

const UNIDADES_INVARIAVEIS = new Set(['kg', 'g', 'l', 'ml']);

const DIA_E_MES = new Intl.DateTimeFormat('pt-BR', { day: '2-digit', month: '2-digit' });

const DIA_DA_SEMANA = new Intl.DateTimeFormat('pt-BR', {
  weekday: 'long',
  day: '2-digit',
  month: '2-digit',
});

export function unidadeNoPlural(quantidade: number, unidade: string): string {
  if (Math.abs(quantidade) === 1 || unidade === '' || UNIDADES_INVARIAVEIS.has(unidade)) {
    return unidade;
  }
  if (unidade.endsWith('s')) {
    return unidade;
  }
  return unidade.endsWith('r') ? `${unidade}es` : `${unidade}s`;
}

export function haQuantoTempo(dias: number): string {
  if (dias === 0) {
    return 'hoje';
  }
  return dias === 1 ? 'ontem' : `há ${dias} dias`;
}

export function saudacaoPara(hora: number): string {
  if (hora < 12) {
    return 'Bom dia';
  }
  return hora < 18 ? 'Boa tarde' : 'Boa noite';
}

export function primeiroNome(nomeCompleto: string): string {
  return nomeCompleto.trim().split(/\s+/)[0] ?? '';
}

export function listarEmPortugues(itens: readonly string[]): string {
  if (itens.length <= 1) {
    return itens.join('');
  }
  return `${itens.slice(0, -1).join(', ')} e ${itens.at(-1)}`;
}

export function dataDaApi(iso: string): Date {
  const [ano = 0, mes = 1, dia = 1] = iso.split('-').map(Number);
  return new Date(ano, mes - 1, dia);
}

export function emFormatoDaApi(data: Date): string {
  const mes = String(data.getMonth() + 1).padStart(2, '0');
  const dia = String(data.getDate()).padStart(2, '0');
  return `${data.getFullYear()}-${mes}-${dia}`;
}

export function comPrimeiraMaiuscula(texto: string): string {
  return texto.charAt(0).toUpperCase() + texto.slice(1);
}

export function colunaDaFatia(fatia: FatiaDoPeriodo, agrupamento: Agrupamento): ColunaDoGrafico {
  const inicio = dataDaApi(fatia.inicio);
  const detalhe =
    agrupamento === 'semana'
      ? `Semana de ${DIA_E_MES.format(inicio)} a ${DIA_E_MES.format(dataDaApi(fatia.fim))}`
      : comPrimeiraMaiuscula(DIA_DA_SEMANA.format(inicio));

  return { rotulo: DIA_E_MES.format(inicio), detalhe, valor: fatia.total };
}

export function descreverVariacao(variacao: number | null, referencia: string): string {
  if (variacao === null) {
    return `sem dados do ${referencia}`;
  }
  if (variacao === 0) {
    return `igual ao ${referencia}`;
  }
  return `${Math.abs(variacao)}% ${variacao > 0 ? 'a mais' : 'a menos'} que no ${referencia}`;
}

export function filtroCompleto(filtro: FiltroDoImpacto): boolean {
  if (filtro.periodo !== 'personalizado') {
    return true;
  }
  return filtro.inicio !== '' && filtro.fim !== '' && filtro.inicio <= filtro.fim;
}

export function parametrosDoFiltro(filtro: FiltroDoImpacto): Record<string, string> {
  return {
    periodo: filtro.periodo,
    ...(filtro.periodo === 'personalizado' && { inicio: filtro.inicio, fim: filtro.fim }),
    ...(filtro.categoria && { categoria: filtro.categoria }),
  };
}
