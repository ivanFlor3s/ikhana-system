import { Component, input } from '@angular/core';
import { MatTableModule } from '@angular/material/table';
import { MatButtonModule } from '@angular/material/button';
import { TableComponent, ColumnDef, ModernTableCellDirective } from '../../../../shared/components/table/table.component';
import { CommonModule } from '@angular/common';
import { Provider } from '../../../../models/provider';

@Component({
  selector: 'app-category-list',
  imports: [MatButtonModule, MatTableModule, TableComponent, ModernTableCellDirective, CommonModule],
  templateUrl: './category-list.component.html',
  styleUrl: './category-list.component.css'
})
export class CategoryListComponent {

  providers = input<Provider[]>([]);
  columns: ColumnDef[] = [
    {
      id: 'id',
      title: 'Provider ID',
      width: 'w-24'
    },
    {
      id: 'name',
      title: 'Provider Name',
    },
    {
      id: 'socialReason',
      title: 'Razon Social',
    },
    {
      id: 'cuit',
      title: 'CUIT',
    },
    {
      id: 'email',
      title: 'Email',
    },
    {
      id: 'phone',
      title: 'Telefono',
    },
    {
      id: 'address',
      title: 'Direccion'
    },
    {
      id: 'actions',
      title: '',
      width: 'w-24'
    }
  ]





}