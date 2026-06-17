import { HttpErrorResponse, HttpInterceptorFn, HttpResponse } from '@angular/common/http';
import { catchError, map, throwError } from 'rxjs';

interface ApiEnvelope<T = unknown> {
  success: boolean;
  data: T;
  message: string;
}

export const unwrapInterceptor: HttpInterceptorFn = (req, next) => {
  return next(req).pipe(
    map(event => {
      if (event instanceof HttpResponse) {
        const body = event.body as ApiEnvelope | null;

        if (body && typeof body === 'object' && 'success' in body && 'data' in body) {
          if (!body.success) {
            throw new HttpErrorResponse({
              error: body,
              headers: event.headers,
              status: 422,
              statusText: body.message ?? 'Request failed',
              url: event.url ?? undefined,
            });
          }

          return event.clone({ body: body.data });
        }
      }

      return event;
    }),
    catchError(error => {
      if (error instanceof HttpErrorResponse && error.status !== 401) {
        console.error(`[API] ${error.status} ${error.statusText}`, error.error);
      }
      return throwError(() => error);
    })
  );
};
