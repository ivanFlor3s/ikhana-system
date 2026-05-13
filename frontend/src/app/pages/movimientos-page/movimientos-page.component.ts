import { Component, inject, signal } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { ProviderInventoryService } from '@services/provider-inventory.service';
import { CoilMovementDto, CoilMovementsResponseDto } from '@interfaces/dtos/response/coils-movements.dto';
import { TableComponent, ColumnDef } from '@shared/components/table/table.component';
import { MatProgressSpinnerModule } from '@angular/material/progress-spinner';
import { take } from 'rxjs';
import { PageHeaderComponent } from "@shared/components/page-header/page-header.component";
import { ArrowRightLeft, LucideAngularModule } from "lucide-angular";

@Component({
  selector: 'app-movimientos-page',
  imports: [TableComponent, MatProgressSpinnerModule, PageHeaderComponent, LucideAngularModule],
  templateUrl: './movimientos-page.component.html',
  styleUrl: './movimientos-page.component.css'
})
export class MovimientosPageComponent {

  private route = inject(ActivatedRoute);
  private router = inject(Router);
  private providerInventoryService = inject(ProviderInventoryService);

  movements = signal<CoilMovementDto[]>([]);
  isLoading = signal<boolean>(false);
  providerId = signal<number | null>(null);
  providerName = signal<string>('');

  readonly arrowIcon = ArrowRightLeft;

  columns: ColumnDef[] = [
    {
      id: 'date',
      title: 'Fecha',
      sortable: true,
      pipe: { name: 'date', args: ['dd/MM/yyyy HH:mm'] }
    },
    {
      id: 'coils_received',
      title: 'Bobinas Recibidas',
      sortable: true,
    },
    {
      id: 'coils_returned',
      title: 'Bobinas Devueltas',
      sortable: true,
    },
    {
      id: 'remito',
      title: 'Remito',
      accessor: (row: CoilMovementDto) => row.entry?.remito ?? '-',
    },
    {
      id: 'entry_number',
      title: 'Nº Ingreso',
      accessor: (row: CoilMovementDto) => row.entry?.entry_number ?? '-',
    },
  ];

  constructor() {
    const nav = this.router.getCurrentNavigation();
    const state = nav?.extras.state as { providerName: string } | undefined;
    if (state?.providerName) {
      this.providerName.set(state.providerName);
    }

    this.route.paramMap.subscribe(params => {
      const id = params.get('providerId');
      if (id) {
        this.providerId.set(+id);
        this.loadMovements(+id);
      }
    });
  }

  loadMovements(providerId: number, page = 1, perPage = 20): void {
    this.isLoading.set(true);

    this.providerInventoryService.getProviderCoilsMovements(providerId, page, perPage).pipe(take(1)).subscribe({
      next: (response: CoilMovementsResponseDto) => {
        this.movements.set(response.data);
        this.isLoading.set(false);
      },
      error: (error) => {
        console.error('Error loading coil movements:', error);
        this.isLoading.set(false);
      },
    });
  }

  onPageChanged(event: { page: number; pageSize: number }): void {
    const id = this.providerId();
    if (id) {
      this.loadMovements(id, event.page, event.pageSize);
    }
  }

}
