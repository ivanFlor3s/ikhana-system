import { Injectable, inject } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { map, Observable } from 'rxjs';
import { environment } from '@environments/environment';
import { CreateEntryRequest } from '@interfaces/dtos/create-entry.dto';
import { CreateEntryResponse } from '@interfaces/dtos/response/create-entry.response.dto';
import { RawMaterialEntriesResponse, RawMaterialCobreEntry } from '@interfaces/dtos/response/raw-material-entries.response';
import { DiametersWithResistanceResponse } from '@interfaces/dtos/response/diameters_with_resistance.response';
import { DiameterIramOhmMaxValue } from '@models/diameters-iram-max-values';
import { ApiResponse } from '@interfaces/pagination.interface';

@Injectable({
    providedIn: 'root'
})
export class CuerdaService {
    private apiUrl = `${environment.apiUrl}/raw-materials`;
    private apiEntriesUrl = `${environment.apiUrl}/cuerda-entries`;
    private http = inject(HttpClient);

    validateResistance(characteristicId: number, resistanceOhmKm: number): Observable<{ success: boolean; data: { valid: boolean; max_allowed: number; measured: number; has_rule: boolean }; message: string }> {
        return this.http.post<{ success: boolean; data: { valid: boolean; max_allowed: number; measured: number; has_rule: boolean }; message: string }>(
            `${this.apiUrl}/validate-resistance`,
            { raw_material_characteristic_id: characteristicId, resistance_ohm_km: resistanceOhmKm }
        );
    }

    getCharacteristicsByType(typeId: number): Observable<{ success: boolean; data: { id: number; name: string; description: string; decimal_value: number; unit: string }[]; message: string }> {
        return this.http.get<{ success: boolean; data: { id: number; name: string; description: string; decimal_value: number; unit: string }[]; message: string }>(
            `${this.apiUrl}/types/${typeId}/characteristics`
        );
    }

    createEntry(request: CreateEntryRequest): Observable<CreateEntryResponse> {
        return this.http.post<CreateEntryResponse>(
            `${this.apiEntriesUrl}`,
            request
        );
    }

    updateEntry(id: number, request: CreateEntryRequest): Observable<CreateEntryResponse> {
        return this.http.put<CreateEntryResponse>(
            `${this.apiEntriesUrl}/${id}`,
            request
        );
    }

    getLastBatch(): Observable<number> {
        return this.http.get<ApiResponse<{ last_batch: string }>>(`${this.apiEntriesUrl}/last-batch`).pipe(map(res => Number(res.data.last_batch)));
    }

    getCuerdaEntries(options?: {
        page?: number;
        per_page?: number;
        search?: string;
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

    getEntryById(id: number): Observable<ApiResponse<RawMaterialCobreEntry>> {
        return this.http.get<ApiResponse<RawMaterialCobreEntry>>(`${this.apiEntriesUrl}/${id}`);
    }

    downloadEntryLabel(entryId: number): Observable<Blob> {
        return this.http.get(
            `${this.apiEntriesUrl}/${entryId}/label`,
            { responseType: 'blob' }
        );
    }

    downloadTestReport(entryId: number): Observable<Blob> {
        return this.http.get(
            `${this.apiEntriesUrl}/${entryId}/test-report`,
            { responseType: 'blob' }
        );
    }

    getDiametersWithIramMaxResistance(): Observable<DiameterIramOhmMaxValue[]> {
        return this.http.get<DiametersWithResistanceResponse>(
            `${this.apiEntriesUrl}/diameters-with-iram-ohm-resistance`
        ).pipe(map(res => res.data.map(d => ({
            diameter: d.decimal_value,
            iramOhmMaxValue: d.iram_copper_ohm_max_resistance
        })))
        );
    }
}
