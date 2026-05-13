import { Injectable, inject, signal } from '@angular/core';
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

    private _taxStatuses = signal<NameValue<number>[]>([]);
    private _agreements = signal<NameValue<number>[]>([]);
    private _categories = signal<NameValue<number>[]>([]);
    private _rawMaterialCharacteristics = signal<NameValue<number>[]>([]);

    private _taxStatusesReadonly = this._taxStatuses.asReadonly();
    private _agreementsReadonly = this._agreements.asReadonly();
    private _categoriesReadonly = this._categories.asReadonly();
    private _rawMaterialCharacteristicsReadonly = this._rawMaterialCharacteristics.asReadonly();

    private _loadingTaxStatuses = false;
    private _loadingAgreements = false;
    private _loadingCategories = false;
    private _loadingRawMaterialChars = false;

    get taxStatuses() {
        if (this._taxStatuses().length === 0 && this.authService.isAuthenticated() && !this._loadingTaxStatuses) {
            this.fetchTaxStatuses();
        }
        return this._taxStatusesReadonly;
    }

    get agreements() {
        if (this._agreements().length === 0 && this.authService.isAuthenticated() && !this._loadingAgreements) {
            this.fetchAgreements();
        }
        return this._agreementsReadonly;
    }

    get categories() {
        if (this._categories().length === 0 && this.authService.isAuthenticated() && !this._loadingCategories) {
            this.fetchCategories();
        }
        return this._categoriesReadonly;
    }

    get rawMaterialCharacteristics() {
        if (this._rawMaterialCharacteristics().length === 0 && this.authService.isAuthenticated() && !this._loadingRawMaterialChars) {
            this.fetchRawMaterialCharacteristics();
        }
        return this._rawMaterialCharacteristicsReadonly;
    }

    private fetchTaxStatuses() {
        this._loadingTaxStatuses = true;
        this.http.get<{ success: boolean, data: TaxStatus[], message: string }>(`${this.apiUrl}/tax-statuses?is_active=1`)
            .pipe(take(1))
            .subscribe({
                next: (response) => {
                    this._taxStatuses.set(response.data.map(item => ({ name: item.name, value: item.id })));
                    this._loadingTaxStatuses = false;
                },
                error: (error) => {
                    console.error('Error fetching tax statuses:', error);
                    this._loadingTaxStatuses = false;
                }
            });
    }

    private fetchAgreements() {
        this._loadingAgreements = true;
        this.http.get<{ success: boolean, data: Agreement[], message: string }>(`${this.apiUrl}/agreements?is_active=1`)
            .pipe(take(1))
            .subscribe({
                next: (response) => {
                    this._agreements.set(response.data.map(item => ({ name: item.name, value: item.id })));
                    this._loadingAgreements = false;
                },
                error: (error) => {
                    console.error('Error fetching agreements:', error);
                    this._loadingAgreements = false;
                }
            });
    }

    private fetchCategories() {
        this._loadingCategories = true;
        this.http.get<{ success: boolean, data: Category[], message: string }>(`${this.apiUrl}/categories`)
            .pipe(take(1))
            .subscribe({
                next: (response) => {
                    this._categories.set(response.data.map(item => ({ name: item.name, value: item.id })));
                    this._loadingCategories = false;
                },
                error: (error) => {
                    console.error('Error fetching categories:', error);
                    this._loadingCategories = false;
                }
            });
    }

    private fetchRawMaterialCharacteristics() {
        this._loadingRawMaterialChars = true;
        this.http.get<{ success: boolean, data: any[], message: string }>(`${this.apiUrl}/raw-materials/types/1/characteristics`)
            .pipe(take(1))
            .subscribe({
                next: (response) => {
                    this._rawMaterialCharacteristics.set(response.data.map(item => ({ name: item.description, value: item.id })));
                    this._loadingRawMaterialChars = false;
                },
                error: (error) => {
                    console.error('Error fetching raw material characteristics:', error);
                    this._loadingRawMaterialChars = false;
                }
            });
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
                ),
                rawMaterialCharacteristics: this.http.get<{ success: boolean, data: any[], message: string }>(
                    `${this.apiUrl}/raw-materials/types/1/characteristics`
                )
            }).pipe(
                tap(response => {
                    // Map API responses to NameValue format and update signals
                    this._taxStatuses.set(response.taxStatuses.data.map(item => ({
                        name: item.name,
                        value: item.id
                    })));

                    this._agreements.set(response.agreements.data.map(item => ({
                        name: item.name,
                        value: item.id
                    })));

                    this._categories.set(response.categories.data.map(item => ({
                        name: item.name,
                        value: item.id
                    })));

                    this._rawMaterialCharacteristics.set(response.rawMaterialCharacteristics.data.map(item => ({
                        name: item.description,
                        value: item.id
                    })));
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
