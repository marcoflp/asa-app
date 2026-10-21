import {
  colunaDaFatia,
  dataDaApi,
  descreverVariacao,
  emFormatoDaApi,
  filtroCompleto,
  haQuantoTempo,
  listarEmPortugues,
  parametrosDoFiltro,
  primeiroNome,
  saudacaoPara,
  unidadeNoPlural,
} from './formatacao';

describe('unidadeNoPlural', () => {
  it.each([
    [1, 'litro', 'litro'],
    [3, 'litro', 'litros'],
    [8, 'unidade', 'unidades'],
    [2, 'par', 'pares'],
    [5, 'kg', 'kg'],
    [4, 'peças', 'peças'],
    [0, 'pacote', 'pacotes'],
  ])('%i %s vira "%s"', (quantidade, unidade, esperado) => {
    expect(unidadeNoPlural(quantidade, unidade)).toBe(esperado);
  });
});

describe('haQuantoTempo', () => {
  it('fala hoje, ontem ou há N dias', () => {
    expect(haQuantoTempo(0)).toBe('hoje');
    expect(haQuantoTempo(1)).toBe('ontem');
    expect(haQuantoTempo(45)).toBe('há 45 dias');
  });
});

describe('saudacaoPara', () => {
  it('muda conforme a hora do dia', () => {
    expect(saudacaoPara(8)).toBe('Bom dia');
    expect(saudacaoPara(14)).toBe('Boa tarde');
    expect(saudacaoPara(21)).toBe('Boa noite');
  });
});

describe('textos', () => {
  it('pega só o primeiro nome', () => {
    expect(primeiroNome('  Marco Antônio Dias ')).toBe('Marco');
  });

  it('lista itens como se fala em português', () => {
    expect(listarEmPortugues([])).toBe('');
    expect(listarEmPortugues(['Fralda'])).toBe('Fralda');
    expect(listarEmPortugues(['Fralda', 'Leite', 'Roupa'])).toBe('Fralda, Leite e Roupa');
  });

  it('descreve a variação contra o período anterior', () => {
    expect(descreverVariacao(null, 'período anterior')).toBe('sem dados do período anterior');
    expect(descreverVariacao(0, 'período anterior')).toBe('igual ao período anterior');
    expect(descreverVariacao(15, 'período anterior')).toBe('15% a mais que no período anterior');
    expect(descreverVariacao(-4, 'dia anterior')).toBe('4% a menos que no dia anterior');
  });
});

describe('datas', () => {
  it('lê a data da API sem mudar de dia por causa do fuso', () => {
    const data = dataDaApi('2026-10-17');
    expect([data.getFullYear(), data.getMonth(), data.getDate()]).toEqual([2026, 9, 17]);
    expect(emFormatoDaApi(data)).toBe('2026-10-17');
  });

  it('descreve colunas por dia e por semana', () => {
    expect(colunaDaFatia({ inicio: '2026-10-17', fim: '2026-10-17', total: 12 }, 'dia')).toEqual({
      rotulo: '17/10',
      detalhe: 'Sábado, 17/10',
      valor: 12,
    });
    expect(
      colunaDaFatia({ inicio: '2026-10-12', fim: '2026-10-18', total: 30 }, 'semana').detalhe,
    ).toBe('Semana de 12/10 a 18/10');
  });
});

describe('filtro do impacto', () => {
  const filtro = {
    periodo: 'personalizado' as const,
    inicio: '2026-10-01',
    fim: '2026-10-20',
    categoria: null,
  };

  it('só fica completo com início antes do fim', () => {
    expect(filtroCompleto({ ...filtro, periodo: '30-dias', inicio: '', fim: '' })).toBe(true);
    expect(filtroCompleto(filtro)).toBe(true);
    expect(filtroCompleto({ ...filtro, fim: '' })).toBe(false);
    expect(filtroCompleto({ ...filtro, inicio: '2026-10-21' })).toBe(false);
  });

  it('manda as datas só no período personalizado e a categoria quando escolhida', () => {
    expect(parametrosDoFiltro({ ...filtro, periodo: '7-dias' })).toEqual({ periodo: '7-dias' });
    expect(parametrosDoFiltro({ ...filtro, categoria: 'Higiene' })).toEqual({
      periodo: 'personalizado',
      inicio: '2026-10-01',
      fim: '2026-10-20',
      categoria: 'Higiene',
    });
  });
});
