import { DatePipe } from '@angular/common';
import { ChangeDetectionStrategy, Component, computed, input, output, signal } from '@angular/core';
import { Icone, NomeDoIcone } from '../compartilhado/icone';
import { ConfiguracaoDoPainel } from '../configuracao';
import { haQuantoTempo, listarEmPortugues, unidadeNoPlural } from '../formatacao';
import { PontosDeAtencao } from '../modelos';

type Assunto = 'atendidas' | 'estoque' | 'ausentes' | 'documentos';

interface Resumo {
  readonly assunto: Assunto;
  readonly rotulo: string;
  readonly icone: NomeDoIcone;
  readonly valor: number;
  readonly complemento: string;
  readonly pendente: boolean;
}

const SABADO = 6;

@Component({
  selector: 'asa-hoje',
  changeDetection: ChangeDetectionStrategy.OnPush,
  imports: [DatePipe, Icone],
  templateUrl: './hoje.html',
  styleUrl: './hoje.css',
})
export class Hoje {
  readonly configuracao = input.required<ConfiguracaoDoPainel>();
  readonly pontos = input<PontosDeAtencao>();
  readonly falhou = input(false);

  readonly recarregar = output();

  protected readonly hoje = new Date();
  protected readonly temAtendimentoHoje = this.hoje.getDay() === SABADO;
  protected readonly aberto = signal<Assunto | null>(null);

  protected readonly resumos = computed<readonly Resumo[]>(() => {
    const pontos = this.pontos();
    if (!pontos) {
      return [];
    }

    const acabaram = pontos.estoqueAcabando.filter((produto) => produto.estoque <= 0).length;
    const meses = Math.round(pontos.diasSemVir / 30);

    return [
      {
        assunto: 'atendidas',
        rotulo: 'Atendidas hoje',
        icone: 'prancheta',
        valor: pontos.atendidasHoje.length,
        complemento: pontos.atendidasHoje.length === 1 ? 'família' : 'famílias',
        pendente: false,
      },
      {
        assunto: 'estoque',
        rotulo: 'Estoque acabando',
        icone: 'caixa',
        valor: pontos.estoqueAcabando.length,
        complemento:
          acabaram > 0
            ? `produtos, ${acabaram} já ${acabaram === 1 ? 'acabou' : 'acabaram'}`
            : 'produtos com menos de 10',
        pendente: pontos.estoqueAcabando.length > 0,
      },
      {
        assunto: 'ausentes',
        rotulo: `Sem vir há ${meses} meses`,
        icone: 'pessoa-ausente',
        valor: pontos.familiasSemVir.length,
        complemento: 'famílias para ligar',
        pendente: pontos.familiasSemVir.length > 0,
      },
      {
        assunto: 'documentos',
        rotulo: 'Sem documento',
        icone: 'documento',
        valor: pontos.cadastrosSemDocumento,
        complemento: 'cadastros sem foto',
        pendente: pontos.cadastrosSemDocumento > 0,
      },
    ];
  });

  protected readonly urgentesPorExtenso = computed(() =>
    listarEmPortugues(this.pontos()?.produtosUrgentes ?? []),
  );

  protected readonly haQuantoTempo = haQuantoTempo;
  protected readonly unidadeNoPlural = unidadeNoPlural;

  protected alternar(assunto: Assunto): void {
    this.aberto.update((atual) => (atual === assunto ? null : assunto));
  }
}
