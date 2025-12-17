import { Injectable, inject } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import { Provider } from '../models/provider.model';
import { CreateProviderDto } from '../interfaces/dtos/create-provider.dto';
import { environment } from '../../environments/environment';
import { ApiResponse, PaginatedResponse, ProviderFilters } from '../interfaces/pagination.interface';

@Injectable({
  providedIn: 'root'
})
export class ProviderService {
  private apiUrl = `${environment.apiUrl}/providers`;
  private http = inject(HttpClient);

  getProviders(filters?: ProviderFilters): Observable<ApiResponse<PaginatedResponse<Provider>>> {
    let params = new HttpParams();

    if (filters) {
      if (filters.page) {
        params = params.set('page', filters.page.toString());
      }
      if (filters.per_page) {
        params = params.set('per_page', filters.per_page.toString());
      }
      if (filters.search) {
        params = params.set('search', filters.search);
      }
      if (filters.category_id) {
        params = params.set('category_id', filters.category_id.toString());
      }
    }

    return this.http.get<ApiResponse<PaginatedResponse<Provider>>>(this.apiUrl, { params });
  }

  createProvider(provider: CreateProviderDto): Observable<{ success: boolean, data: Provider, message: string }> {
    return this.http.post<{ success: boolean, data: Provider, message: string }>(this.apiUrl, provider);
  }

  getProviderById(id: number): Observable<{ success: boolean, data: Provider, message: string }> {
    return this.http.get<{ success: boolean, data: Provider, message: string }>(`${this.apiUrl}/${id}`);
  }

  updateProvider(id: number, provider: CreateProviderDto): Observable<{ success: boolean, data: Provider, message: string }> {
    return this.http.put<{ success: boolean, data: Provider, message: string }>(`${this.apiUrl}/${id}`, provider);
  }

  deleteProvider(id: number): Observable<{ success: boolean, message: string }> {
    return this.http.delete<{ success: boolean, message: string }>(`${this.apiUrl}/${id}`);
  }
}
