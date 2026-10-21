import { DatePipe } from '@angular/common';
import { ChangeDetectionStrategy, Component, computed, input } from '@angular/core';
import { Icone } from '../compartilhado/icone';
import { ConfiguracaoDoPainel } from '../configuracao';
import { primeiroNome, saudacaoPara } from '../formatacao';

const SABADO = 6;

@Component({
  selector: 'asa-cabecalho',
  changeDetection: ChangeDetectionStrategy.OnPush,
  imports: [DatePipe, Icone],
  templateUrl: './cabecalho.html',
  styleUrl: './cabecalho.css',
})
export class Cabecalho {
  readonly configuracao = input.required<ConfiguracaoDoPainel>();

  protected readonly agora = new Date();

  protected readonly saudacao = computed(
    () => `${saudacaoPara(this.agora.getHours())}, ${primeiroNome(this.configuracao().usuario)}`,
  );

  protected readonly temAtendimentoHoje = this.agora.getDay() === SABADO;
}
