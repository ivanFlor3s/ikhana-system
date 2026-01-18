import { Component, input } from '@angular/core';
import { IngresoCobre } from '@interfaces/mocks/cobre-ingreso-interface';
import { ColumnDef, TableComponent } from '@shared/components/table/table.component';

@Component({
  selector: 'app-mp-stock-cobre-list',
  imports: [TableComponent],
  templateUrl: './mp-stock-cobre-list.component.html',
  styleUrl: './mp-stock-cobre-list.component.css'
})
export class MpStockCobreListComponent {

  data = input.required<IngresoCobre[]>();

  columns: ColumnDef[] = [
    {
      id: 'fecha',
      title: 'Fecha',
      // Using pipe for date formatting
      pipe: { name: 'date', args: ['dd/MM/yyyy'] }
    },
    {
      id: 'remito',
      title: 'Remito',
      // Using formatter function to add prefix
      formatter: (value) => `#${value}`
    },
    {
      id: 'provider',
      title: 'Proveedor',
      // Using pipe for text transformation
      pipe: { name: 'titlecase' }
    },
    {
      id: 'weight',
      title: 'Peso',
      // Using formatter to add unit and pipe for number formatting
      formatter: (value) => parseFloat(value),
      pipe: { name: 'number', args: ['1.2-2'] }
    },
    {
      id: 'diameter',
      title: 'Diámetro',
      // Using formatter to add unit suffix
      formatter: (value) => `${value} mm`
    },
    {
      id: 'lote',
      title: 'Lote',
      pipe: { name: 'uppercase' }
    },
    {
      id: 'validations',
      title: 'Validaciones',
      // Custom formatter to display validation count
      formatter: (value) => {
        if (!value) return 'N/A';
        const passed = Object.values(value).filter(v => v === true).length;
        const total = Object.keys(value).length;
        return `${passed}/${total}`;
      }
    },
    {
      id: 'resultado',
      title: 'Resultado',
      // Custom formatter with conditional styling logic
      formatter: (value) => value || 'Pendiente'
    },
    {
      id: 'dateOfTest',
      title: 'Fecha de Test',
      // Using pipe with custom date format
      pipe: { name: 'date', args: ['dd/MM/yyyy HH:mm'] }
    }
  ]



}
