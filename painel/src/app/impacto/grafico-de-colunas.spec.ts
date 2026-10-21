import { ComponentFixture, TestBed } from '@angular/core/testing';
import { GraficoDeColunas } from './grafico-de-colunas';

describe('GraficoDeColunas', () => {
  let fixture: ComponentFixture<GraficoDeColunas>;
  let elemento: HTMLElement;

  async function montar(valores: number[]): Promise<void> {
    fixture = TestBed.createComponent(GraficoDeColunas);
    fixture.componentRef.setInput('titulo', 'Retiradas por dia');
    fixture.componentRef.setInput('unidade', { singular: 'retirada', plural: 'retiradas' });
    fixture.componentRef.setInput(
      'colunas',
      valores.map((valor, indice) => ({
        rotulo: `0${indice + 1}/10`,
        detalhe: `Dia ${indice + 1}`,
        valor,
      })),
    );
    await fixture.whenStable();
    elemento = fixture.nativeElement;
  }

  it('desenha uma coluna por fatia, proporcional ao maior valor', async () => {
    await montar([5, 10, 0]);

    const barras = [...elemento.querySelectorAll<HTMLElement>('.barra')];
    expect(barras.map((barra) => barra.style.blockSize)).toEqual(['50%', '100%', '0%']);
    expect(elemento.querySelector('.coluna')?.getAttribute('aria-label')).toBe(
      'Dia 1: 5 retiradas',
    );
  });

  it('mostra a dica ao passar o mouse e apaga as outras colunas', async () => {
    await montar([5, 1]);

    elemento.querySelectorAll('.coluna')[1]?.dispatchEvent(new Event('mouseenter'));
    await fixture.whenStable();

    expect(elemento.querySelector('.dica')?.textContent).toContain('1 retirada');
    expect(elemento.querySelectorAll('.coluna.apagada')).toHaveLength(1);
  });

  it('troca o gráfico por uma tabela', async () => {
    await montar([3, 4]);

    elemento.querySelector<HTMLButtonElement>('header button')?.click();
    await fixture.whenStable();

    expect(elemento.querySelectorAll('tbody tr')).toHaveLength(2);
    expect(elemento.querySelector('.colunas')).toBeNull();
  });

  it('avisa quando não houve retirada', async () => {
    await montar([0, 0]);

    expect(elemento.textContent).toContain('Nenhuma retirada neste período.');
  });
});
