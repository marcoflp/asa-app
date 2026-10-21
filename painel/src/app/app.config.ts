import { provideHttpClient, withFetch, withInterceptors } from '@angular/common/http';
import {
  ApplicationConfig,
  LOCALE_ID,
  provideBrowserGlobalErrorListeners,
  provideZonelessChangeDetection,
} from '@angular/core';
import { aceitarJson } from './compartilhado/aceitar-json';

export const configuracaoDoApp: ApplicationConfig = {
  providers: [
    provideBrowserGlobalErrorListeners(),
    provideZonelessChangeDetection(),
    provideHttpClient(withFetch(), withInterceptors([aceitarJson])),
    { provide: LOCALE_ID, useValue: 'pt-BR' },
  ],
};
