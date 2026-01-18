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
      pipe: { name: 'date', args: ['dd/MM/yy'] }
    },
    {
      id: 'remito',
      title: 'Remito'
    },
    {
      id: 'pesoKg',
      title: 'Peso (kg)',
      pipe: { name: 'number', args: ['1.2-2'] }
    },
    {
      id: 'diametroAnteriorMm',
      title: 'Diámetro anterior (mm)',
      pipe: { name: 'number', args: ['1.2-2'] }
    },
    {
      id: 'lote',
      title: 'Lote'
    },
    {
      id: 'identificacionEmbalaje',
      title: 'Identificación Embalaje'
    },
    {
      id: 'aspectoSuperficialLibreDefectos',
      title: 'Aspecto Superficial Libre de Defectos'
    },
    {
      id: 'resistenciaOhmsKm',
      title: 'Resistencia (ohms/km)',
      pipe: { name: 'number', args: ['1.2-2'] }
    },
    {
      id: 'recocidoPercent',
      title: 'Recocido %',
      pipe: { name: 'number', args: ['1.2-2'] }
    },
    {
      id: 'resultado',
      title: 'Resultado'
    },
    {
      id: 'fechaEnsayo',
      title: 'Fecha Ensayo',
      pipe: { name: 'date', args: ['dd/MM/yy'] }
    },
  ]

}
