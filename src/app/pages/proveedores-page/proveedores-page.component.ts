import { Component, inject, OnInit, signal } from '@angular/core';
import { Provider } from '../../models/provider.model';
import { MatDialog } from '@angular/material/dialog';
import { ProviderCreateOrEdit } from '../../modules/proveedores/dialogs/provider-create-or-edit/provider-create-or-edit';
import { ProviderService } from '../../services/provider.service';
import { ProviderFormData } from '../../interfaces/form-data-models/provider-form-data.model';
import { mapProviderFormToDto } from '../../interfaces/mappers/provider-form.mapper';
import { ProveedoresListComponent } from '../../modules/proveedores/components/proveedores-list/proveedores-list.component';
import { ProveedoresHeader } from '../../modules/proveedores/components/proveedores-header/proveedores-header';
import { ProveedoresFilterComponent } from '../../modules/proveedores/components/proveedores-filter/proveedores-filter.component';
import { RubroService } from '../../services/rubro.service';
import { NameValue } from '@models/name-value.model';
import { MatProgressSpinnerModule } from '@angular/material/progress-spinner';
import { MatPaginatorModule, PageEvent } from '@angular/material/paginator';
import { ProviderFilters } from '../../interfaces/pagination.interface';

@Component({
    selector: 'app-proveedores-page',
    imports: [
        ProveedoresListComponent,
        ProveedoresHeader,
        ProveedoresFilterComponent,
        MatProgressSpinnerModule,
        MatPaginatorModule
    ],
    templateUrl: './proveedores-page.component.html',
    styleUrl: './proveedores-page.component.css'
})
export class ProveedoresPageComponent implements OnInit {
    readonly dialog = inject(MatDialog);
    readonly providerService = inject(ProviderService);
    readonly rubroService = inject(RubroService);

    // State signals
    providers = signal<Provider[]>([]);
    rubros = signal<NameValue[]>([]);
    isLoading = signal<boolean>(false);

    // Pagination state
    totalProviders = signal<number>(0);
    pageSize = signal<number>(15);
    currentPage = signal<number>(1);

    // Filter state
    currentFilters = signal<ProviderFilters>({
        page: 1,
        per_page: 15
    });

    ngOnInit(): void {
        this.loadRubros();
        this.loadProviders();
    }

    loadRubros(): void {
        this.rubroService.getAllRubros().subscribe({
            next: (rubros) => {
                this.rubros.set(rubros);
            },
            error: (error) => {
                console.error('Error loading rubros:', error);
            }
        });
    }

    loadProviders(): void {
        this.isLoading.set(true);

        this.providerService.getProviders(this.currentFilters()).subscribe({
            next: (response) => {
                this.providers.set(response.data.data);
                this.totalProviders.set(response.data.total);
                this.currentPage.set(response.data.current_page);
                this.pageSize.set(response.data.per_page);
                this.isLoading.set(false);
            },
            error: (error) => {
                console.error('Error loading providers:', error);
                this.isLoading.set(false);
            }
        });
    }

    onFilterChange(filters: { search?: string; categoryIds?: number[] }): void {
        // Update filters
        const newFilters: ProviderFilters = {
            page: 1, // Reset to first page when filters change
            per_page: this.pageSize(),
            search: filters.search || undefined,
            category_id: filters.categoryIds && filters.categoryIds.length > 0
                ? filters.categoryIds[0]
                : undefined
        };

        this.currentFilters.set(newFilters);
        this.loadProviders();
    }

    onPageChange(event: PageEvent): void {
        const newFilters: ProviderFilters = {
            ...this.currentFilters(),
            page: event.pageIndex + 1, // Material paginator is 0-indexed
            per_page: event.pageSize
        };

        this.currentFilters.set(newFilters);
        this.loadProviders();
    }

    openProviderDialog() {
        console.log('Open provider dialog');

        const dialogRef = this.dialog.open(ProviderCreateOrEdit, {
            width: '800px',
        });

        dialogRef.afterClosed().subscribe((result: ProviderFormData) => {
            if (result) {
                console.log('Form data received:', result);

                // Map form data to API DTO
                const createDto = mapProviderFormToDto(result);
                console.log('Mapped DTO:', createDto);

                // Call the service to create the provider
                this.providerService.createProvider(createDto).subscribe({
                    next: (response) => {
                        console.log('Provider created successfully:', response);
                        // Reload providers list
                        this.loadProviders();
                    },
                    error: (error) => {
                        console.error('Error creating provider:', error);
                    }
                });
            } else {
                console.log('Dialog was closed without submitting');
            }
        });
    }

    onProviderDeleted(): void {
        // Reload the providers list after successful deletion
        this.loadProviders();
    }
}
