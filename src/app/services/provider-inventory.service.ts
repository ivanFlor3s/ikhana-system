import { HttpClient } from "@angular/common/http";
import { inject, Injectable } from "@angular/core";
import { environment } from "@environments/environment";
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
}
