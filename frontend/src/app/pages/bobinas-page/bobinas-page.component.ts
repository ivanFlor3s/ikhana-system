import { Component, inject, OnInit, signal } from '@angular/core';
import { PageHeaderComponent } from "@shared/components/page-header/page-header.component";
import { Spool, LucideAngularModule } from 'lucide-angular';
import { BobinasListComponent } from "@modules/proveedores/components/bobinas-list/bobinas-list.component";
import { BobinasFilterComponent } from "@modules/proveedores/components/bobinas-filter/bobinas-filter.component";
import { BobinasService } from '@services/bobinas.service';
import { CoilsSummaryItemDto } from '@interfaces/dtos/response/coils-summary-item.dto';
import { CoilsSummaryDto } from '@interfaces/dtos/coils-summary.dto';
import { MatProgressSpinnerModule } from '@angular/material/progress-spinner';
import { SortDirection } from '@shared/components/table/table.component';
import { take } from 'rxjs';
import { MatDialog } from '@angular/material/dialog';
import { CreateOrUpdateBobinasProveedorComponent } from '@modules/proveedores/dialogs/create-or-update-bobinas-proveedor/create-or-update-bobinas-proveedor.component';
import { Button } from "@shared/components/button/button";

@Component({
  selector: 'app-bobinas-page',
  imports: [PageHeaderComponent, LucideAngularModule, BobinasListComponent, BobinasFilterComponent, MatProgressSpinnerModule, Button],
  templateUrl: './bobinas-page.component.html',
  styleUrl: './bobinas-page.component.css'
})
export class BobinasPageComponent implements OnInit {
  private bobinasService = inject(BobinasService);
  private dialogService = inject(MatDialog);

  readonly SpoonIcon = Spool;
  coils = signal<CoilsSummaryItemDto[]>([]);
  isLoading = signal<boolean>(false);

  currentFilters = signal<CoilsSummaryDto>({
    page: 1,
    per_page: 15,
  });

  ngOnInit(): void {
    this.loadCoils();
  }

  loadCoils(): void {
    this.isLoading.set(true);

    this.bobinasService.getCoilsSummary(this.currentFilters()).pipe(take(1)).subscribe({
      next: (response) => {
        this.coils.set(response.data);
        this.isLoading.set(false);
      },
      error: (error) => {
        console.error('Error loading coils summary:', error);
        this.isLoading.set(false);
      },
    });
  }

  onFilterChange(filters: { search?: string }): void {
    this.currentFilters.set({
      ...this.currentFilters(),
      page: 1,
      search: filters.search,
    });
    this.loadCoils();
  }

  onSortChanged(event: { columnId: string | null; direction: SortDirection }): void {
    this.currentFilters.set({
      ...this.currentFilters(),
      sort_by: event.columnId ?? undefined,
      sort_dir: event.direction ?? undefined,
    });
    this.loadCoils();
  }

  createClicked() {
    this.dialogService.open(CreateOrUpdateBobinasProveedorComponent, {
      width: 'auto',
      data: { providerId: null }
    }).afterClosed().subscribe((result) => {
      if (result) {
        this.loadCoils();
      }
    });
  }

  onEditClicked(coil: CoilsSummaryItemDto) {
    this.dialogService.open(CreateOrUpdateBobinasProveedorComponent, {
      width: 'auto',
      data: {
        providerId: coil.provider_id,
        providerName: coil.provider_name,
        currentCoils: coil.coils_count,
      }
    }).afterClosed().subscribe((result) => {
      if (result) {
        this.loadCoils();
      }
    });
  }
}
