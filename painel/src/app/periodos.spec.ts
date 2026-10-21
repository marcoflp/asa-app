import { enderecoComPeriodo, periodoDoEndereco } from './periodos';

describe('período no endereço da página', () => {
  it('usa 30 dias quando não há período ou ele é desconhecido', () => {
    expect(periodoDoEndereco('https://asapf.me/dashboard')).toBe('30-dias');
    expect(periodoDoEndereco('https://asapf.me/dashboard?periodo=1-ano')).toBe('30-dias');
  });

  it('lê o período escolhido', () => {
    expect(periodoDoEndereco('https://asapf.me/dashboard?periodo=3-meses')).toBe('3-meses');
  });

  it('grava o período sem perder outros parâmetros e limpa quando volta ao padrão', () => {
    expect(enderecoComPeriodo('https://asapf.me/dashboard?aba=1', '6-meses')).toBe(
      'https://asapf.me/dashboard?aba=1&periodo=6-meses',
    );
    expect(enderecoComPeriodo('https://asapf.me/dashboard?periodo=6-meses', '30-dias')).toBe(
      'https://asapf.me/dashboard',
    );
  });
});
