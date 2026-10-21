import { DecimalPipe } from '@angular/common';
import { ChangeDetectionStrategy, Component, computed, input } from '@angular/core';
import { Icone, NomeDoIcone } from '../compartilhado/icone';
import { descreverVariacao } from '../formatacao';
import { Indicador } from '../modelos';

@Component({
  selector: 'asa-indicador',
  changeDetection: ChangeDetectionStrategy.OnPush,
  host: { class: 'cartao' },
  imports: [DecimalPipe, Icone],
  template: `
    <p class="rotulo"><asa-icone [nome]="icone()" /> {{ rotulo() }}</p>
    <p class="valor">{{ indicador().valor | number }}</p>
    <p class="variacao">
      @if (indicador().variacao; as variacao) {
        <asa-icone [nome]="variacao > 0 ? 'subiu' : 'desceu'" />
      }
      {{ variacaoPorExtenso() }}
    </p>
  `,
  styles: `
    p {
      margin: 0;
    }
    .rotulo {
      display: flex;
      align-items: center;
      gap: 0.5rem;
      font-size: 0.875rem;
      font-weight: 500;
      color: var(--texto-secundario);
    }
    .rotulo asa-icone {
      inline-size: 1rem;
      block-size: 1rem;
    }
    .valor {
      margin-top: 0.5rem;
      font-size: clamp(1.875rem, 4vw, 2.25rem);
      font-weight: 700;
      letter-spacing: -0.02em;
    }
    .variacao {
      display: flex;
      align-items: center;
      gap: 0.25rem;
      margin-top: 0.25rem;
      font-size: 0.75rem;
      color: var(--texto-discreto);
    }
    .variacao asa-icone {
      inline-size: 0.875rem;
      block-size: 0.875rem;
      color: var(--texto);
    }
  `,
})
export class IndicadorDoPeriodo {
  readonly rotulo = input.required<string>();
  readonly icone = input.required<NomeDoIcone>();
  readonly indicador = input.required<Indicador>();
  readonly referencia = input.required<string>();

  protected readonly variacaoPorExtenso = computed(() =>
    descreverVariacao(this.indicador().variacao, this.referencia()),
  );
}
