/**
 * AuthApiService — typed auth endpoints
 * ======================================
 *
 * Uses generated types matching the .NET AuthController response DTOs.
 * The unwrapInterceptor strips { success, data, message } so methods
 * return the raw DTO types directly.
 */

import { Injectable, inject } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { environment } from '../../environments/environment';
import type { components } from '@generated/api-types';

export type LoginRequest = components['schemas']['LoginRequest'];
export type AuthResponse = components['schemas']['AuthResponse'];
export type MeResponse = components['schemas']['MeResponse'];

@Injectable({ providedIn: 'root' })
export class AuthApiService {
  private apiUrl = `${environment.apiUrl}/auth`;
  private http = inject(HttpClient);

  /** POST /api/auth/login. After unwrap: AuthResponse (with token). */
  login(credentials: LoginRequest): Observable<AuthResponse> {
    return this.http.post<AuthResponse>(`${this.apiUrl}/login`, credentials);
  }

  /** POST /api/auth/logout. After unwrap: void (no data in envelope). */
  logout(): Observable<void> {
    return this.http.post<void>(`${this.apiUrl}/logout`, {});
  }

  /** GET /api/auth/me. After unwrap: MeResponse. */
  me(): Observable<MeResponse> {
    return this.http.get<MeResponse>(`${this.apiUrl}/me`);
  }
}
