import { HttpInterceptorFn } from '@angular/common/http';

export const aceitarJson: HttpInterceptorFn = (requisicao, proximo) =>
  proximo(requisicao.clone({ setHeaders: { Accept: 'application/json' } }));
