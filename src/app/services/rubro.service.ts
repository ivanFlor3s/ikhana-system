import { Injectable, inject } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable, map } from 'rxjs';
import { environment } from '../../environments/environment';
import { NameValue } from '@models/name-value.model';
import { Rubro } from '../models/rubro.model';
import { CreateRubroDto, UpdateRubroDto } from '../interfaces/dtos/create-rubro.dto';

interface RubroResponse {
    id: number;
    name: string;
    description: string;
    created_at: string;
    updated_at: string;
    deleted_at: string | null;
}

interface ApiResponse {
    success: boolean;
    data: RubroResponse[];
    message: string;
}

interface SingleRubroResponse {
    success: boolean;
    data: Rubro;
    message: string;
}

@Injectable({
    providedIn: 'root'
})
export class RubroService {
    private http = inject(HttpClient);
    private apiUrl = `${environment.apiUrl}/categories`;

    // Get all rubros as NameValue pairs (for filters/dropdowns)
    getAllRubros(): Observable<NameValue[]> {
        return this.http.get<ApiResponse>(this.apiUrl).pipe(
            map(response =>
                response.data.map(rubro => ({
                    name: rubro.name,
                    value: rubro.id
                }))
            )
        );
    }

    // Get all rubros with full details
    getRubros(): Observable<ApiResponse> {
        return this.http.get<ApiResponse>(this.apiUrl);
    }

    // Get a specific rubro by ID
    getRubroById(id: number): Observable<SingleRubroResponse> {
        return this.http.get<SingleRubroResponse>(`${this.apiUrl}/${id}`);
    }

    // Create a new rubro
    createRubro(rubro: CreateRubroDto): Observable<SingleRubroResponse> {
        return this.http.post<SingleRubroResponse>(this.apiUrl, rubro);
    }

    // Update an existing rubro
    updateRubro(id: number, rubro: UpdateRubroDto): Observable<SingleRubroResponse> {
        return this.http.put<SingleRubroResponse>(`${this.apiUrl}/${id}`, rubro);
    }

    // Delete a rubro (soft delete)
    deleteRubro(id: number): Observable<{ success: boolean; message: string }> {
        return this.http.delete<{ success: boolean; message: string }>(`${this.apiUrl}/${id}`);
    }
}
