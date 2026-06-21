/**
 * DEMO: ProviderApiService — demonstrates the full type pipeline
 * ===============================================================
 *
 * Pipeline: C# DTO → OpenAPI spec → openapi-typescript → TypeScript types
 * Interceptor: unwrapInterceptor strips { success, data, message } envelope
 *               authInterceptor adds Bearer token
 *
 * Usage: Use this alongside ProviderService during migration.
 *        When the migration is complete, replace ProviderService with this.
 */

import { Injectable, inject } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable, map } from 'rxjs';
import { environment } from '@environments/environment';
import { toHttpParams } from '@shared/utils/to-http-params';
import type { components } from '@generated/api-types';

// ── Generated types (auto-generated from backend OpenAPI spec) ──────────

export type ProviderListResponse = components['schemas']['ProviderListResponse'];
export type ProviderDetailResponse = components['schemas']['ProviderDetailResponse'];
export type PaginatedProviderList = components['schemas']['PaginatedListOfProviderListResponse'];
export type ProviderListItem = components['schemas']['ProviderListResponse'];
export type CreateProviderCommand = components['schemas']['CreateProviderCommand'];
export type UpdateProviderCommand = components['schemas']['UpdateProviderCommand'];

export interface ProviderFilters {
  page?: number;
  pageSize?: number;
  search?: string;
  categoryId?: number;
}

@Injectable({ providedIn: 'root' })
export class ProviderApiService {
  private apiUrl = `${environment.apiUrl}/providers`;
  private http = inject(HttpClient);

  /**
   * Paginated list. The unwrapInterceptor strips { success, data, message }
   * so we receive PaginatedProviderList directly.
   */
  list(filters?: ProviderFilters): Observable<PaginatedProviderList> {
    const params = toHttpParams({
      page: filters?.page,
      pageSize: filters?.pageSize,
      search: filters?.search,
      categoryId: filters?.categoryId,
    });
    return this.http.get<PaginatedProviderList>(this.apiUrl, { params });
  }

  /**
   * Create — returns unwrapped ProviderDetailResponse.
   * Type-safe: command matches the C# CreateProviderCommand DTO exactly.
   */
  create(command: CreateProviderCommand): Observable<ProviderDetailResponse> {
    return this.http.post<ProviderDetailResponse>(this.apiUrl, command);
  }

  /** Show — returns unwrapped ProviderDetailResponse. */
  get(id: number): Observable<ProviderDetailResponse> {
    return this.http.get<ProviderDetailResponse>(`${this.apiUrl}/${id}`);
  }

  /** Update */
  update(id: number, command: UpdateProviderCommand): Observable<ProviderDetailResponse> {
    return this.http.put<ProviderDetailResponse>(`${this.apiUrl}/${id}`, command);
  }

  /** Delete (no content on success) */
  delete(id: number): Observable<void> {
    return this.http.delete<void>(`${this.apiUrl}/${id}`).pipe(map(() => undefined));
  }

  /** Summary list */
  summary(): Observable<components['schemas']['ProviderSummaryResponse'][]> {
    return this.http.get<components['schemas']['ProviderSummaryResponse'][]>(`${this.apiUrl}/summary`);
  }
}
