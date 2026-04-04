import { Component, inject, signal } from '@angular/core';
import { MpCobreHeaderComponent } from "@modules/materias-primas/cobre/components/mp-cobre-header/mp-cobre-header.component";
import { MpStockCobreListComponent } from "@modules/materias-primas/cobre/components/mp-stock-cobre-list/mp-stock-cobre-list.component";
import { MpCobreFilterComponent, RawMaterialFilterOptions } from "@modules/materias-primas/cobre/components/mp-cobre-filter/mp-cobre-filter.component";
import { ActivatedRoute, Router } from '@angular/router';
import { RawMaterialCobreEntry } from '@interfaces/dtos/response/raw-material-entries.response';
import { RawMaterialService } from '@services/raw-material.service';
import { MatProgressSpinner } from "@angular/material/progress-spinner";

@Component({
  selector: 'app-materias-primas-cobre-page',
  imports: [MpCobreHeaderComponent, MpStockCobreListComponent, MpCobreFilterComponent, MatProgressSpinner],
  templateUrl: './materias-primas-cobre-page.component.html',
  styleUrl: './materias-primas-cobre-page.component.css'
})
export class MateriasPrimasCobrePageComponent {
  private router = inject(Router);
  private route = inject(ActivatedRoute);
  private rawMaterialService = inject(RawMaterialService);

  data = signal<RawMaterialCobreEntry[]>([]);
  isLoading = signal<boolean>(false);
  currentFilters: RawMaterialFilterOptions = {};

  ngOnInit(): void {
    this.loadData();
  }

  loadData(filters?: RawMaterialFilterOptions) {
    this.isLoading.set(true);

    // Merge filters with the raw_material_type_id for Cobre
    const queryParams = {
      raw_material_type_id: 1,
      ...filters
    };

    this.rawMaterialService.getRawMaterialEntries(queryParams).subscribe({
      next: (response) => {
        this.data.set(response.data);
        this.isLoading.set(false);
      },
      error: (error) => {
        console.error('Error loading data:', error);
        this.isLoading.set(false);
      }
    });
  }

  onFilterChange(filters: RawMaterialFilterOptions) {
    this.currentFilters = filters;
    this.loadData(filters);
  }

  onNuevoIngreso() {
    this.router.navigate(['ingreso'], { relativeTo: this.route });
  }

}
