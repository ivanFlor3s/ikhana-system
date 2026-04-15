import { Component, input, output } from '@angular/core';
import { TableComponent, ColumnDef, SortDirection } from '@shared/components/table/table.component';
import { CoilsSummaryItemDto } from '@interfaces/dtos/response/coils-summary-item.dto';

@Component({
  selector: 'app-bobinas-list',
  imports: [TableComponent],
  templateUrl: './bobinas-list.component.html',
  styleUrl: './bobinas-list.component.css'
})
export class BobinasListComponent {
  coils = input<CoilsSummaryItemDto[]>([]);

  sortChanged = output<{ columnId: string | null; direction: SortDirection }>();

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
  ];

  onSortChanged(event: { columnId: string | null; direction: SortDirection }): void {
    this.sortChanged.emit(event);
  }
}
