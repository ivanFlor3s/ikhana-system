/**
 * CategoryApiService — typed category CRUD
 * =========================================
 *
 * Powered by the same type-generation pipeline as ProviderApiService:
 *   C# DTO → OpenAPI → openapi-typescript → TypeScript
 *
 * The unwrapInterceptor strips { success, data, message } so every
 * method returns the raw DTO (no envelope to unwrap manually).
 */

import { Injectable, inject } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable, map } from 'rxjs';
import { environment } from '@environments/environment';
import { toHttpParams } from '@shared/utils/to-http-params';
import type { components } from '@generated/api-types';

// ── Generated types ─────────────────────────────────────────────────────

export type CategoryListResponse = components['schemas']['CategoryResponse'];
export type CategoryDetailResponse = components['schemas']['CategoryDetailResponse'];
export type PaginatedCategoryList = components['schemas']['PaginatedListOfCategoryResponse'];
export type CreateCategoryCommand = components['schemas']['CreateCategoryCommand'];
export type UpdateCategoryCommand = components['schemas']['UpdateCategoryCommand'];

@Injectable({ providedIn: 'root' })
export class CategoryApiService {
  private apiUrl = `${environment.apiUrl}/categories`;
  private http = inject(HttpClient);

  /**
   * Paginated list.
   * After unwrap: PaginatedCategoryList → items[] are CategoryListResponse.
   */
  list(page = 1, pageSize = 15): Observable<PaginatedCategoryList> {
    const params = toHttpParams({ page, pageSize });
    return this.http.get<PaginatedCategoryList>(this.apiUrl, { params });
  }

  /** Show. After unwrap: CategoryDetailResponse. */
  get(id: number): Observable<CategoryDetailResponse> {
    return this.http.get<CategoryDetailResponse>(`${this.apiUrl}/${id}`);
  }

  /** Create. Returns unwrapped CategoryDetailResponse. */
  create(command: CreateCategoryCommand): Observable<CategoryDetailResponse> {
    return this.http.post<CategoryDetailResponse>(this.apiUrl, command);
  }

  /** Update. Returns unwrapped CategoryDetailResponse. */
  update(id: number, command: UpdateCategoryCommand): Observable<CategoryDetailResponse> {
    return this.http.put<CategoryDetailResponse>(`${this.apiUrl}/${id}`, command);
  }

  /** Soft-delete. No content on success. */
  delete(id: number): Observable<void> {
    return this.http.delete<void>(`${this.apiUrl}/${id}`).pipe(map(() => undefined));
  }
}
