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

    getBrokerById(id: number): Observable<{ success: boolean, data: Broker, message: string }> {
        return this.http.get<{ success: boolean, data: Broker, message: string }>(`${this.apiUrl}/${id}`);
    }

    updateBroker(id: number, broker: CreateBrokerDto): Observable<{ success: boolean, data: Broker, message: string }> {
        return this.http.put<{ success: boolean, data: Broker, message: string }>(`${this.apiUrl}/${id}`, broker);
    }

    deleteBroker(id: number): Observable<{ success: boolean, message: string }> {
        return this.http.delete<{ success: boolean, message: string }>(`${this.apiUrl}/${id}`);
    }
}
