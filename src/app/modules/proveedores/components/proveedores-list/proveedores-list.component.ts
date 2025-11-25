import { Component, input, inject } from '@angular/core';
import { MatTableModule } from '@angular/material/table';
import { MatButtonModule } from '@angular/material/button';
import { TableComponent, ColumnDef, ModernTableCellDirective } from '../../../../shared/components/table/table.component';
import { CommonModule } from '@angular/common';
import { Provider } from '../../../../models/provider';
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

    onProviderClick(provider: Provider) {
        this.sideDetailService.open(ProviderDetailComponent, { providerId: provider.id });
    }
}
