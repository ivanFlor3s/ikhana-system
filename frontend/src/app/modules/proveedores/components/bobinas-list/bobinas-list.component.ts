import { Component, inject, input, output } from '@angular/core';
import { TableComponent, ColumnDef, SortDirection, ModernTableCellDirective } from '@shared/components/table/table.component';
import { CoilsSummaryItemDto } from '@interfaces/dtos/response/coils-summary-item.dto';
import { ArrowRightLeft, Pencil, LucideAngularModule } from "lucide-angular";
import { Router } from '@angular/router';
import { MatTooltip } from "@angular/material/tooltip";

@Component({
  selector: 'app-bobinas-list',
  imports: [TableComponent, LucideAngularModule, ModernTableCellDirective, MatTooltip],
  templateUrl: './bobinas-list.component.html',
  styleUrl: './bobinas-list.component.css'
})
export class BobinasListComponent {
  coils = input<CoilsSummaryItemDto[]>([]);

  sortChanged = output<{ columnId: string | null; direction: SortDirection }>();
  editClicked = output<CoilsSummaryItemDto>();

  readonly ArrowRightLeft = ArrowRightLeft;
  readonly Pencil = Pencil;

  columns: ColumnDef<CoilsSummaryItemDto>[] = [
    {
      id: 'provider_name',
      title: 'Proveedor',
      sortable: true,
    },
    {
      id: 'coils_count',
      title: 'Bobinas',
      sortable: true,
      pipe: { name: 'number', args: ['1.0-0'] },
    },
    {
      id: 'last_movement_in',
      title: 'Último ingreso',
      sortable: true,
      pipe: { name: 'date', args: ['dd/MM/yyyy HH:mm'] },
    },
    {
      id: 'last_movement_out',
      title: 'Última salida',
      sortable: true,
      pipe: { name: 'date', args: ['dd/MM/yyyy HH:mm'] },
      formatter: (value: string | null) => value ?? '-',
    },
    {
      id: 'actions',
      title: '',
      width: 'w-24'
    }
  ];

  private router = inject(Router);

  onSortChanged(event: { columnId: string | null; direction: SortDirection }): void {
    this.sortChanged.emit(event);
  }

  goToMovements(coil: CoilsSummaryItemDto): void {
    this.router.navigate(['app', 'proveedores', coil.provider_id, 'movimientos-bobinas'], {
      state: { providerName: coil.provider_name }
    });
  }

  onEditClicked(coil: CoilsSummaryItemDto): void {
    this.editClicked.emit(coil);
  }
}
