import { DecimalPipe, NgTemplateOutlet } from '@angular/common';
import { ChangeDetectionStrategy, Component, computed, input, output } from '@angular/core';
import { BarraHorizontal } from '../modelos';

@Component({
  selector: 'asa-barras-horizontais',
  changeDetection: ChangeDetectionStrategy.OnPush,
  imports: [DecimalPipe, NgTemplateOutlet],
  templateUrl: './barras-horizontais.html',
  styleUrl: './barras-horizontais.css',
})
export class BarrasHorizontais {
  readonly barras = input.required<readonly BarraHorizontal[]>();
  readonly selecionavel = input(false);
  readonly selecionada = input<string | null>(null);
  readonly colunas = input<1 | 2>(1);

  readonly selecionar = output<string>();

  protected readonly maior = computed(() =>
    Math.max(1, ...this.barras().map((barra) => barra.valor)),
  );

  protected largura(valor: number): string {
    return `${(valor / this.maior()) * 100}%`;
  }
}
