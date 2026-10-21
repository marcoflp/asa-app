import { ChangeDetectionStrategy, Component, model } from '@angular/core';
import { emFormatoDaApi } from '../formatacao';
import { Periodo } from '../modelos';
import { PERIODOS } from '../periodos';

@Component({
  selector: 'asa-seletor-de-periodo',
  changeDetection: ChangeDetectionStrategy.OnPush,
  host: { role: 'group', 'aria-label': 'Período' },
  templateUrl: './seletor-de-periodo.html',
  styleUrl: './seletor-de-periodo.css',
})
export class SeletorDePeriodo {
  readonly periodo = model.required<Periodo>();
  readonly inicio = model('');
  readonly fim = model('');

  protected readonly periodos = PERIODOS;
  protected readonly hoje = emFormatoDaApi(new Date());
}
