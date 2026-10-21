import { Periodo } from './modelos';

export const PERIODOS: readonly { readonly valor: Periodo; readonly rotulo: string }[] = [
  { valor: 'hoje', rotulo: 'Hoje' },
  { valor: '7-dias', rotulo: '7 dias' },
  { valor: '30-dias', rotulo: '30 dias' },
  { valor: '3-meses', rotulo: '3 meses' },
  { valor: '6-meses', rotulo: '6 meses' },
  { valor: 'personalizado', rotulo: 'Escolher datas' },
];

export const PERIODO_PADRAO: Periodo = '30-dias';

export function ehPeriodo(valor: string | null): valor is Periodo {
  return PERIODOS.some((periodo) => periodo.valor === valor);
}

export function periodoDoEndereco(endereco: string): Periodo {
  const periodo = new URL(endereco).searchParams.get('periodo');
  return ehPeriodo(periodo) ? periodo : PERIODO_PADRAO;
}

export function enderecoComPeriodo(endereco: string, periodo: Periodo): string {
  const url = new URL(endereco);
  if (periodo === PERIODO_PADRAO) {
    url.searchParams.delete('periodo');
  } else {
    url.searchParams.set('periodo', periodo);
  }
  return url.toString();
}
