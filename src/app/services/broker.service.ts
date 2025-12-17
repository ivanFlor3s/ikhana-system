import { Injectable, inject } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { Broker } from '../models/provider.model';
import { CreateBrokerDto } from '../interfaces/dtos/create-broker.dto';
import { environment } from '../../environments/environment';

@Injectable({
    providedIn: 'root'
})
export class BrokerService {
    private apiUrl = `${environment.apiUrl}/brokers`;
    private http = inject(HttpClient);

    createBroker(broker: CreateBrokerDto): Observable<{ success: boolean, data: Broker, message: string }> {
        return this.http.post<{ success: boolean, data: Broker, message: string }>(this.apiUrl, broker);
    }
}
