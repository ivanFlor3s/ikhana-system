import { Injectable, inject } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { Provider } from '../models/provider.model';
import { CreateProviderDto } from '../interfaces/dtos/create-provider.dto';
import { environment } from '../../environments/environment';

@Injectable({
  providedIn: 'root'
})
export class ProviderService {
  private apiUrl = `${environment.apiUrl}/providers`;
  private http = inject(HttpClient);

  getProviders(): Observable<{ success: boolean, data: Provider[], message: string }> {
    return this.http.get<{ success: boolean, data: Provider[], message: string }>(this.apiUrl);
  }

  createProvider(provider: CreateProviderDto): Observable<{ success: boolean, data: Provider, message: string }> {
    return this.http.post<{ success: boolean, data: Provider, message: string }>(this.apiUrl, provider);
  }

  getProviderById(id: number): Observable<{ success: boolean, data: Provider, message: string }> {
    return this.http.get<{ success: boolean, data: Provider, message: string }>(`${this.apiUrl}/${id}`);
  }

  updateProvider(id: number, provider: Partial<Omit<Provider, 'id' | 'created_at' | 'updated_at' | 'deleted_at'>>): Observable<{ success: boolean, data: Provider, message: string }> {
    return this.http.put<{ success: boolean, data: Provider, message: string }>(`${this.apiUrl}/${id}`, provider);
  }

  deleteProvider(id: number): Observable<{ success: boolean, message: string }> {
    return this.http.delete<{ success: boolean, message: string }>(`${this.apiUrl}/${id}`);
  }
}
