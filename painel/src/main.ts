import { registerLocaleData } from '@angular/common';
import localePt from '@angular/common/locales/pt';
import { createCustomElement } from '@angular/elements';
import { createApplication } from '@angular/platform-browser';
import { configuracaoDoApp } from './app/app.config';
import { Painel } from './app/painel';

const NOME_DO_ELEMENTO = 'asa-painel';

registerLocaleData(localePt);

createApplication(configuracaoDoApp)
  .then((app) => {
    if (!customElements.get(NOME_DO_ELEMENTO)) {
      customElements.define(
        NOME_DO_ELEMENTO,
        createCustomElement(Painel, { injector: app.injector }),
      );
    }
  })
  .catch((erro: unknown) => console.error(erro));
