import { ChangeDetectionStrategy, Component, computed, input, signal } from '@angular/core';
import { Icone } from '../compartilhado/icone';
import { ConfiguracaoDoPainel } from '../configuracao';
import { haQuantoTempo, listarEmPortugues, unidadeNoPlural } from '../formatacao';
import { PontosDeAtencao } from '../modelos';

const PRODUTOS_VISIVEIS = 4;
const FAMILIAS_VISIVEIS_DE_INICIO = 4;

@Component({
  selector: 'asa-precisa-de-atencao',
  changeDetection: ChangeDetectionStrategy.OnPush,
  host: { class: 'cartao' },
  imports: [Icone],
  templateUrl: './precisa-de-atencao.html',
  styleUrl: './precisa-de-atencao.css',
})
export class PrecisaDeAtencao {
  readonly pontos = input.required<PontosDeAtencao>();
  readonly rotas = input.required<ConfiguracaoDoPainel['rotas']>();

  protected readonly todasAsFamilias = signal(false);

  protected readonly produtosVisiveis = computed(() =>
    this.pontos().estoqueAcabando.slice(0, PRODUTOS_VISIVEIS),
  );

  protected readonly familiasVisiveis = computed(() =>
    this.todasAsFamilias()
      ? this.pontos().familiasSemVir
      : this.pontos().familiasSemVir.slice(0, FAMILIAS_VISIVEIS_DE_INICIO),
  );

  protected readonly mesesSemVir = computed(() => Math.round(this.pontos().diasSemVir / 30));

  protected readonly urgentesPorExtenso = computed(() =>
    listarEmPortugues(this.pontos().produtosUrgentes),
  );

  protected readonly haQuantoTempo = haQuantoTempo;
  protected readonly unidadeNoPlural = unidadeNoPlural;
  protected readonly familiasVisiveisDeInicio = FAMILIAS_VISIVEIS_DE_INICIO;
}
