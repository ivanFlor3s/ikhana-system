import { Component, inject, OnInit, signal } from '@angular/core';
import { MatDialog } from '@angular/material/dialog';
import { ProviderCreateOrEdit } from '../../modules/proveedores/dialogs/provider-create-or-edit/provider-create-or-edit';
import { ProveedoresListComponent } from '../../modules/proveedores/components/proveedores-list/proveedores-list.component';
import { ProveedoresHeader } from '../../modules/proveedores/components/proveedores-header/proveedores-header';
import { ProveedoresFilterComponent } from '../../modules/proveedores/components/proveedores-filter/proveedores-filter.component';
import { NameValue } from '@models/name-value.model';
import { MatProgressSpinnerModule } from '@angular/material/progress-spinner';
import { MatPaginatorModule, PageEvent } from '@angular/material/paginator';
import { AppInitService } from '@services/app-init.service';
import { ProviderApiService, ProviderListItem, ProviderFilters } from '@generated/provider-api.service';
import { TABLE_FIRST_PAGE, TABLE_PAGE_SIZE } from 'app/constants/table';

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
    readonly providerApi = inject(ProviderApiService);

    private appInitService = inject(AppInitService);

    providers = signal<ProviderListItem[]>([]);
    rubros = signal<NameValue[]>([]);
    isLoading = signal<boolean>(false);

    totalProviders = signal<number>(0);
    pageSize = signal<number>(TABLE_PAGE_SIZE);
    currentPage = signal<number>(TABLE_FIRST_PAGE);

    currentFilters = signal<ProviderFilters>({
        page: 1,
        pageSize: 15
    });

    get categories() {
        return this.appInitService.categories;
    }

    ngOnInit(): void {
        this.loadProviders();
    }

    loadProviders(): void {
        this.isLoading.set(true);

        this.providerApi.list(this.currentFilters()).subscribe({
            next: (response) => {
                this.providers.set(response.items ?? []);
                this.totalProviders.set(Number(response.totalCount) ?? 0);
                this.currentPage.set(Number(response.page) ?? TABLE_FIRST_PAGE);
                this.pageSize.set(Number(response.pageSize) ?? TABLE_PAGE_SIZE);
                this.isLoading.set(false);
            },
            error: (error) => {
                console.error('Error loading providers:', error);
                this.isLoading.set(false);
            }
        });
    }

    onFilterChange(filters: { search?: string; categoryIds?: number[] }): void {
        const newFilters: ProviderFilters = {
            page: 1,
            pageSize: this.pageSize(),
            search: filters.search || undefined,
            categoryId: filters.categoryIds && filters.categoryIds.length > 0
                ? filters.categoryIds[0]
                : undefined
        };

        this.currentFilters.set(newFilters);
        this.loadProviders();
    }

    onPageChange(event: PageEvent): void {
        const newFilters: ProviderFilters = {
            ...this.currentFilters(),
            page: event.pageIndex + 1,
            pageSize: event.pageSize
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
