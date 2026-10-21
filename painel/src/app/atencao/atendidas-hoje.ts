import { ChangeDetectionStrategy, Component, computed, input, signal } from '@angular/core';
import { RetiradaDeHoje } from '../modelos';

const VISIVEIS_DE_INICIO = 5;

@Component({
  selector: 'asa-atendidas-hoje',
  changeDetection: ChangeDetectionStrategy.OnPush,
  host: { class: 'cartao' },
  templateUrl: './atendidas-hoje.html',
  styleUrl: './atendidas-hoje.css',
})
export class AtendidasHoje {
  readonly retiradas = input.required<readonly RetiradaDeHoje[]>();
  readonly enderecoDasRetiradas = input.required<string>();

  protected readonly mostrandoTodas = signal(false);

  protected readonly visiveis = computed(() =>
    this.mostrandoTodas() ? this.retiradas() : this.retiradas().slice(0, VISIVEIS_DE_INICIO),
  );

  protected readonly temMais = computed(() => this.retiradas().length > VISIVEIS_DE_INICIO);
}
