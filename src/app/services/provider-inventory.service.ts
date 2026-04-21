import { HttpClient, HttpParams } from "@angular/common/http";
import { inject, Injectable } from "@angular/core";
import { environment } from "@environments/environment";
import { CoilMovementsResponseDto } from "@interfaces/dtos/response/coils-movements.dto";
import { ProviderInventoryResponse } from "@interfaces/dtos/response/provider-inventory.response";
import { Observable } from "rxjs";

@Injectable({
    providedIn: 'root'
})
export class ProviderInventoryService {
    private apiUrl = `${environment.apiUrl}/providers`;
    private http = inject(HttpClient);

    constructor() {
    }

    getProviderInventory(providerId: number): Observable<ProviderInventoryResponse> {
        return this.http.get<ProviderInventoryResponse>(`${this.apiUrl}/${providerId}/inventory`);
    }

    getProviderCoilsMovements(providerId: number, page = 1, perPage = 20): Observable<CoilMovementsResponseDto> {
        const params = new HttpParams()
            .set('page', page.toString())
            .set('per_page', perPage.toString());
        return this.http.get<CoilMovementsResponseDto>(`${this.apiUrl}/${providerId}/coil-movements`, { params });
    }
}
