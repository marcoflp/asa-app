import { ChangeDetectionStrategy, Component, computed, input, signal } from '@angular/core';
import { ColunaDoGrafico } from '../modelos';

const ROTULOS_NO_EIXO = 10;

@Component({
  selector: 'asa-grafico-de-colunas',
  changeDetection: ChangeDetectionStrategy.OnPush,
  host: { class: 'cartao' },
  templateUrl: './grafico-de-colunas.html',
  styleUrl: './grafico-de-colunas.css',
})
export class GraficoDeColunas {
  readonly titulo = input.required<string>();
  readonly colunas = input.required<readonly ColunaDoGrafico[]>();
  readonly unidade = input.required<{ readonly singular: string; readonly plural: string }>();

  protected readonly destacada = signal<number | null>(null);
  protected readonly comoTabela = signal(false);

  protected readonly maior = computed(() =>
    Math.max(1, ...this.colunas().map((coluna) => coluna.valor)),
  );

  protected readonly vazio = computed(() => this.colunas().every((coluna) => coluna.valor === 0));

  protected readonly intervaloDosRotulos = computed(() =>
    Math.max(1, Math.ceil(this.colunas().length / ROTULOS_NO_EIXO)),
  );

  protected altura(valor: number): string {
    return valor === 0 ? '0%' : `${Math.max(2, (valor / this.maior()) * 100)}%`;
  }

  protected posicaoDaDica(indice: number): 'inicio' | 'meio' | 'fim' {
    const terco = this.colunas().length / 3;
    if (indice < terco) {
      return 'inicio';
    }
    return indice > terco * 2 ? 'fim' : 'meio';
  }

  protected quantidade(valor: number): string {
    return `${valor} ${valor === 1 ? this.unidade().singular : this.unidade().plural}`;
  }
}
