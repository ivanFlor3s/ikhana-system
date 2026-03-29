import { Injectable, inject } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { map, Observable } from 'rxjs';
import { environment } from '../../environments/environment';
import { CreateEntryRequest } from '@interfaces/dtos/create-entry.dto';
import { CreateEntryResponse } from '@interfaces/dtos/response/create-entry.response.dto';
import { RawMaterialEntriesResponse } from '@interfaces/dtos/response/raw-material-entries.response';
import { DiametersWithResistanceResponse } from '@interfaces/dtos/response/diameters_with_resistance.response';
import { DiameterIramOhmMaxValue } from '@models/diameters-iram-max-values';

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

    /**
     * Gets a paginated list of raw material entries with filtering options
     * @param options - Query parameters for pagination and filtering
     * @returns Observable with paginated entries response
     */
    getRawMaterialEntries(options?: {
        page?: number;
        per_page?: number;
        search?: string;
        raw_material_type_id?: number;
        raw_material_characteristic_id?: number;
        date_from?: string;
        date_to?: string;
    }): Observable<RawMaterialEntriesResponse> {
        let params = new HttpParams();

        if (options) {
            if (options.page !== undefined) {
                params = params.set('page', options.page.toString());
            }
            if (options.per_page !== undefined) {
                params = params.set('per_page', options.per_page.toString());
            }
            if (options.search) {
                params = params.set('search', options.search);
            }
            if (options.raw_material_type_id !== undefined) {
                params = params.set('raw_material_type_id', options.raw_material_type_id.toString());
            }
            if (options.raw_material_characteristic_id !== undefined) {
                params = params.set('raw_material_characteristic_id', options.raw_material_characteristic_id.toString());
            }
            if (options.date_from) {
                params = params.set('date_from', options.date_from);
            }
            if (options.date_to) {
                params = params.set('date_to', options.date_to);
            }
        }

        return this.http.get<RawMaterialEntriesResponse>(
            `${this.apiEntriesUrl}`,
            { params }
        );
    }

    /**
     * Downloads a PDF label for a raw material entry
     * @param entryId - The entry ID
     * @returns Observable with blob response
     */
    downloadEntryLabel(entryId: number): Observable<Blob> {
        return this.http.get(
            `${this.apiEntriesUrl}/${entryId}/label`,
            { responseType: 'blob' }
        );
    }

    getCobreDiametersWithIramMaxResistance(): Observable<DiameterIramOhmMaxValue[]> {
        return this.http.get<DiametersWithResistanceResponse>(
            `${this.apiUrl}/diameters-with-iram-ohm-resistance`
        ).pipe(map(res => res.data.map(d => ({
            diameter: d.decimal_value,
            iramOhmMaxValue: d.iram_copper_ohm_max_resistance
        })))
        );
    }
}
