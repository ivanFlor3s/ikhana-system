import { Injectable, inject } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { environment } from '../../environments/environment';
import { CreateEntryRequest } from '@interfaces/dtos/create-entry.dto';
import { CreateEntryResponse } from '@interfaces/dtos/response/create-entry.response.dto';

export interface ValidateResistanceRequest {
    raw_material_characteristic_id: number;
    resistance_ohm_km: number;
}

export interface ValidateResistanceResponse {
    success: boolean;
    data: {
        valid: boolean;
        max_allowed: number;
        measured: number;
        has_rule: boolean;
    };
    message: string;
}

export interface RawMaterialCharacteristic {
    id: number;
    name: string;
    description: string;
    decimal_value: number;
    unit: string;
}

export interface CharacteristicsResponse {
    success: boolean;
    data: RawMaterialCharacteristic[];
    message: string;
}

@Injectable({
    providedIn: 'root'
})
export class RawMaterialService {
    private apiUrl = `${environment.apiUrl}/raw-materials`;
    private apiEntriesUrl = `${environment.apiUrl}/raw-material-entries`;
    private http = inject(HttpClient);

    /**
     * Validates resistance against IRAM standard
     * @param request - Contains characteristic ID and measured resistance
     * @returns Observable with validation result
     */
    validateResistance(request: ValidateResistanceRequest): Observable<ValidateResistanceResponse> {
        return this.http.post<ValidateResistanceResponse>(
            `${this.apiUrl}/validate-resistance`,
            request
        );
    }

    /**
     * Gets characteristics for a specific raw material type
     * @param typeId - The raw material type ID (1 for Cobre/Copper)
     * @returns Observable with characteristics list
     */
    getCharacteristicsByType(typeId: number): Observable<CharacteristicsResponse> {
        return this.http.get<CharacteristicsResponse>(
            `${this.apiUrl}/types/${typeId}/characteristics`
        );
    }

    /**
     * Creates a new entry for a raw material
     * @param request - Contains the entry data
     * @returns Observable with the created entry response
     */
    createEntry(request: CreateEntryRequest): Observable<CreateEntryResponse> {
        return this.http.post<CreateEntryResponse>(
            `${this.apiEntriesUrl}`,
            request
        );
    }
}
