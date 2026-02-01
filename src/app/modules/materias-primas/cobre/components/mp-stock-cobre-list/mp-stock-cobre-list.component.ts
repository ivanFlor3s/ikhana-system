import { Component, input, SimpleChanges } from '@angular/core';
import { RawMaterialEntry } from '@interfaces/dtos/response/raw-material-entries.response';
import { ColumnDef, TableComponent } from '@shared/components/table/table.component';

@Component({
  selector: 'app-mp-stock-cobre-list',
  imports: [TableComponent],
  templateUrl: './mp-stock-cobre-list.component.html',
  styleUrl: './mp-stock-cobre-list.component.css'
})
export class MpStockCobreListComponent {

  data = input.required<RawMaterialEntry[]>();

  ngOnInit(): void {
    console.log(this.data());
  }

  ngOnChanges(changes: SimpleChanges): void {
    console.log(changes);
  }

  columns: ColumnDef[] = [
    {
      id: 'entry_date',
      title: 'Fecha',
      pipe: { name: 'date', args: ['dd/MM/yy'] }
    },
    {
      id: 'batch',
      title: 'N° Entrada'
    },
    {
      id: 'remito',
      title: 'Remito'
    },
    {
      id: 'provider',
      formatter(value, row) {
        return row.provider.business_name;
      },
      title: 'Proveedor'
    },
    {
      id: 'quantity_kg',
      title: 'Peso (kg)',
      pipe: { name: 'number', args: ['1.0-0'] }
    },
    {
      id: 'coils_count',
      title: 'Bobinas',
      pipe: { name: 'number', args: ['1.0-0'] }
    },
    {
      id: 'characteristic',
      formatter(value, row) {
        return row.characteristic.description;
      },
      title: 'Diámetro'
    },
    {
      id: 'batch',
      title: 'Lote'
    },
    {
      id: 'test.resistance_ohm_km',
      title: 'Resistencia (Ω/km)',
      formatter(value, row) {
        return row.test.resistance_ohm_km;
      },
      pipe: { name: 'number', args: ['1.2-2'] }
    },
    {
      id: 'test.result',
      title: 'Resultado',
      formatter(value, row) {
        return row.test.result;
      },
    },
    {
      id: 'status',
      title: 'Estado'
    },
  ]

}
