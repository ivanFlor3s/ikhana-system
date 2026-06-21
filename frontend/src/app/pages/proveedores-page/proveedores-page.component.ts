import { Component, inject, OnInit, signal } from '@angular/core';
import { Provider } from '../../models/provider.model';
import { MatDialog } from '@angular/material/dialog';
import { ProviderCreateOrEdit } from '../../modules/proveedores/dialogs/provider-create-or-edit/provider-create-or-edit';
import { ProviderService } from '../../services/provider.service';
import { ProveedoresListComponent } from '../../modules/proveedores/components/proveedores-list/proveedores-list.component';
import { ProveedoresHeader } from '../../modules/proveedores/components/proveedores-header/proveedores-header';
import { ProveedoresFilterComponent } from '../../modules/proveedores/components/proveedores-filter/proveedores-filter.component';
import { NameValue } from '@models/name-value.model';
import { MatProgressSpinnerModule } from '@angular/material/progress-spinner';
import { MatPaginatorModule, PageEvent } from '@angular/material/paginator';
import { ProviderFilters } from '../../interfaces/pagination.interface';
import { AppInitService } from '@services/app-init.service';

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

    private appInitService = inject(AppInitService);

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

    get categories() {
        return this.appInitService.categories;
    }

    ngOnInit(): void {
        this.loadProviders();
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

        dialogRef.afterClosed().subscribe((result) => {
            if (result) {
                console.log('Provider dialog closed after success:', result);
                // Reload providers list since the modal handled the API call
                this.loadProviders();
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
