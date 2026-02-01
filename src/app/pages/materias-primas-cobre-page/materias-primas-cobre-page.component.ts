import { Component, inject, signal } from '@angular/core';
import { MpCobreHeaderComponent } from "@modules/materias-primas/cobre/components/mp-cobre-header/mp-cobre-header.component";
import { MpStockCobreListComponent } from "@modules/materias-primas/cobre/components/mp-stock-cobre-list/mp-stock-cobre-list.component";
import { ActivatedRoute, Router } from '@angular/router';
import { RawMaterialEntry } from '@interfaces/dtos/response/raw-material-entries.response';
import { RawMaterialService } from '@services/raw-material.service';
import { MatProgressSpinner } from "@angular/material/progress-spinner";

@Component({
  selector: 'app-materias-primas-cobre-page',
  imports: [MpCobreHeaderComponent, MpStockCobreListComponent, MatProgressSpinner],
  templateUrl: './materias-primas-cobre-page.component.html',
  styleUrl: './materias-primas-cobre-page.component.css'
})
export class MateriasPrimasCobrePageComponent {
  private router = inject(Router);
  private route = inject(ActivatedRoute);
  private rawMaterialService = inject(RawMaterialService);

  data = signal<RawMaterialEntry[]>([]);
  isLoading = signal<boolean>(false);

  ngOnInit(): void {
    this.loadData();
  }

  loadData() {
    this.isLoading.set(true);
    this.rawMaterialService.getRawMaterialEntries({ raw_material_type_id: 1 }).subscribe({
      next: (response) => {
        this.data.set(response.data.data);
        this.isLoading.set(false);
      },
      error: (error) => {
        console.error('Error loading data:', error);
        this.isLoading.set(false);
      }
    });
  }

  onNuevoIngreso() {
    this.router.navigate(['ingreso'], { relativeTo: this.route });
  }

}
