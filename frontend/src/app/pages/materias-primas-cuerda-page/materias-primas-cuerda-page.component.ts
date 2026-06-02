import { Component, OnInit, signal, inject } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { DatePipe } from '@angular/common';
import { MatProgressSpinnerModule } from '@angular/material/progress-spinner';
import { MpCuerdaHeaderComponent } from '@modules/materias-primas/cuerda/components/mp-cuerda-header/mp-cuerda-header.component';
import { MpCobreFilterComponent, RawMaterialFilterOptions } from '@modules/materias-primas/cobre/components/mp-cobre-filter/mp-cobre-filter.component';
import { MpStockCobreListComponent } from '@modules/materias-primas/cobre/components/mp-stock-cobre-list/mp-stock-cobre-list.component';
import { CuerdaService } from '@services/cuerda.service';
import { RawMaterialCobreEntry } from '@interfaces/dtos/response/raw-material-entries.response';

@Component({
    selector: 'app-materias-primas-cuerda-page',
    imports: [
    MatProgressSpinnerModule,
    MpCuerdaHeaderComponent,
    MpCobreFilterComponent,
    MpStockCobreListComponent,
],
    providers: [DatePipe],
    templateUrl: './materias-primas-cuerda-page.component.html',
    styleUrl: './materias-primas-cuerda-page.component.css',
})
export class MateriasPrimasCuerdaPageComponent implements OnInit {
    private cuerdaService = inject(CuerdaService);
    private router = inject(Router);
    private route = inject(ActivatedRoute);

    data = signal<RawMaterialCobreEntry[]>([]);
    isLoading = signal(false);
    currentFilters = signal<RawMaterialFilterOptions>({});

    ngOnInit(): void {
        this.loadData();
    }

    loadData(filters?: RawMaterialFilterOptions): void {
        if (filters) {
            this.currentFilters.set(filters);
        }
        this.isLoading.set(true);
        this.cuerdaService.getCuerdaEntries(this.currentFilters()).subscribe({
            next: (response) => {
                this.data.set(response.data);
                this.isLoading.set(false);
            },
            error: () => {
                this.isLoading.set(false);
            },
        });
    }

    onFilterChange(filters: RawMaterialFilterOptions): void {
        this.loadData(filters);
    }

    onNuevoIngreso(): void {
        this.router.navigate(['ingreso'], { relativeTo: this.route });
    }
}
