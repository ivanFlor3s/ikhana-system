import { HttpParams } from '@angular/common/http';

/**
 * Converts a plain object of query parameters into Angular HttpParams.
 * Skips keys with undefined or null values.
 *
 * @example
 *   const params = toHttpParams({ page: 1, pageSize: 15, search: 'test' });
 *   this.http.get('/api/items', { params });
 */
export function toHttpParams(params: Record<string, string | number | boolean | undefined | null>): HttpParams {
  let httpParams = new HttpParams();
  for (const [key, value] of Object.entries(params)) {
    if (value != null && value !== '') {
      httpParams = httpParams.set(key, value.toString());
    }
  }
  return httpParams;
}
