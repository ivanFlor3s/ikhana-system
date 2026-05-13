import { HttpClient, HttpParams } from "@angular/common/http";
import { inject, Injectable } from "@angular/core";
import { environment } from "@environments/environment";
import { CoilsSummaryDto } from "@interfaces/dtos/coils-summary.dto";
import { CoilsSummaryResponseDto } from "@interfaces/dtos/response/coils-summary-item.dto";
import { Observable } from "rxjs";

@Injectable({
    providedIn: 'root'
})
export class BobinasService {
    private readonly baseUrl = `${environment.apiUrl}/providers`;
    private http = inject(HttpClient);

    getCoilsSummary(params: CoilsSummaryDto): Observable<CoilsSummaryResponseDto> {
        let httpParams = new HttpParams();
        if (params.page) {
            httpParams = httpParams.set('page', params.page.toString());
        }
        if (params.per_page) {
            httpParams = httpParams.set('per_page', params.per_page.toString());
        }
        if (params.search) {
            httpParams = httpParams.set('search', params.search);
        }
        if (params.sort_by) {
            httpParams = httpParams.set('sort_by', params.sort_by);
        }
        if (params.sort_dir) {
            httpParams = httpParams.set('sort_dir', params.sort_dir);
        }
        return this.http.get<CoilsSummaryResponseDto>(`${this.baseUrl}/coil-summary`, { params: httpParams });

    }


}
