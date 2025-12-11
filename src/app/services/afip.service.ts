import { Injectable, inject } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { environment } from '../../environments/environment';

export interface TaxIdValidationResponse {
    tax_id: string;
    exists: boolean;
}

@Injectable({
    providedIn: 'root'
})
export class AfipService {
    private apiUrl = `${environment.apiUrl}/tax-id`;
    private http = inject(HttpClient);

    validateTaxId(taxId: string): Observable<TaxIdValidationResponse> {
        return this.http.post<TaxIdValidationResponse>(`${this.apiUrl}/validate`, { tax_id: taxId });
    }
}
