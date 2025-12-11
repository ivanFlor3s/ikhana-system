import { Component, input, inject } from '@angular/core';
import { MatTableModule } from '@angular/material/table';
import { MatButtonModule } from '@angular/material/button';
import { TableComponent, ColumnDef, ModernTableCellDirective } from '../../../../shared/components/table/table.component';
import { CommonModule } from '@angular/common';
import { Provider } from '../../../../models/provider.model';
import { SideDetailService } from '../../../../services/side-detail.service';
import { ProviderDetailComponent } from '../provider-detail/provider-detail.component';

@Component({
    selector: 'app-proveedores-list',
    imports: [MatButtonModule, MatTableModule, TableComponent, ModernTableCellDirective, CommonModule],
    templateUrl: './proveedores-list.component.html',
    styleUrl: './proveedores-list.component.css'
})
export class ProveedoresListComponent {
    private sideDetailService = inject(SideDetailService);

    providers = input<Provider[]>([]);
    columns: ColumnDef[] = [
        {
            id: 'id',
            title: 'ID',
            width: 'w-20'
        },
        {
            id: 'fantasy_name',
            title: 'Nombre',
        },
        {
            id: 'rubro',
            title: 'Rubro',
            accessor: (row: Provider) => row.category?.name || '-'
        },
        {
            id: 'business_name',
            title: 'Razón Social',
        },
        {
            id: 'cuit',
            title: 'CUIT',
        },
        {
            id: 'email_1',
            title: 'Email',
        },
        {
            id: 'phone_1',
            title: 'Teléfono',
        },
        {
            id: 'address',
            title: 'Dirección'
        },
        {
            id: 'actions',
            title: '',
            width: 'w-24'
        }
    ]

    onProviderClick(provider: Provider) {
        this.sideDetailService.open(ProviderDetailComponent, { providerId: provider.id });
    }
}
