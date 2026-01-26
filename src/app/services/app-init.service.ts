import { Injectable, inject } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { forkJoin, take, tap } from 'rxjs';
import { environment } from '../../environments/environment';
import { TaxStatus } from '../core/models/tax-status.model';
import { Agreement } from '../core/models/agreement.model';
import { Category } from '../core/models/category.model';
import { NameValue } from '../models/name-value.model';
import { AuthService } from '@services/auth.service';

@Injectable({
    providedIn: 'root'
})
export class AppInitService {
    private http = inject(HttpClient);
    private authService = inject(AuthService);

    private apiUrl = environment.apiUrl;

    private _taxStatuses: NameValue<number>[] = [];
    private _agreements: NameValue<number>[] = [];
    private _categories: NameValue<number>[] = [];

    get taxStatuses(): NameValue<number>[] {
        return this._taxStatuses;
    }

    get agreements(): NameValue<number>[] {
        return this._agreements;
    }

    get categories(): NameValue<number>[] {
        return this._categories;
    }

    /**
     * Loads reference data from the API on app startup
     * This is called by APP_INITIALIZER in app.config.ts
     */
    loadReferenceData(): Promise<void> {
        const userIsAuthenticated = this.authService.isAuthenticated();
        if (!userIsAuthenticated) {
            return Promise.resolve();
        }
        return new Promise((resolve, reject) => {
            forkJoin({
                taxStatuses: this.http.get<{ success: boolean, data: TaxStatus[], message: string }>(
                    `${this.apiUrl}/tax-statuses?is_active=1`
                ),
                agreements: this.http.get<{ success: boolean, data: Agreement[], message: string }>(
                    `${this.apiUrl}/agreements?is_active=1`
                ),
                categories: this.http.get<{ success: boolean, data: Category[], message: string }>(
                    `${this.apiUrl}/categories`
                )
            }).pipe(
                tap(response => {
                    // Map API responses to NameValue format
                    this._taxStatuses = response.taxStatuses.data.map(item => ({
                        name: item.name,
                        value: item.id
                    }));

                    this._agreements = response.agreements.data.map(item => ({
                        name: item.name,
                        value: item.id
                    }));

                    this._categories = response.categories.data.map(item => ({
                        name: item.name,
                        value: item.id
                    }));
                }),
                take(1)
            ).subscribe({
                next: () => resolve(),
                error: (error) => {
                    console.error('Error loading reference data:', error);
                    // Resolve anyway to allow app to start even if reference data fails
                    resolve();
                }
            });
        });
    }
}
