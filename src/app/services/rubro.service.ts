import { Injectable, inject } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable, map } from 'rxjs';
import { environment } from '../../environments/environment';
import { NameValue } from '@models/name-value.model';

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

@Injectable({
    providedIn: 'root'
})
export class RubroService {
    private http = inject(HttpClient);
    private apiUrl = `${environment.apiUrl}/categories`;

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
}
