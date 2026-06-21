import { Component, input, inject, output } from '@angular/core';
import { MatTableModule } from '@angular/material/table';
import { MatButtonModule } from '@angular/material/button';
import { MatDialog } from '@angular/material/dialog';
import { TableComponent, ColumnDef, ModernTableCellDirective } from '@shared/components/table/table.component';
import { CommonModule } from '@angular/common';
import { SideDetailService } from '@services/side-detail.service';
import { ProviderDetailComponent } from '../provider-detail/provider-detail.component';
import { ProviderApiService, ProviderListItem } from '@generated/provider-api.service';
import { ConfirmDialogComponent } from '@shared/components/confirm-dialog/confirm-dialog.component';
import { NotificationService } from '@services/notification.service';
import { ProviderCreateOrEdit } from '@modules/proveedores/dialogs/provider-create-or-edit/provider-create-or-edit';

@Component({
    selector: 'app-proveedores-list',
    imports: [MatButtonModule, MatTableModule, TableComponent, ModernTableCellDirective, CommonModule],
    templateUrl: './proveedores-list.component.html',
    styleUrl: './proveedores-list.component.css'
})
export class ProveedoresListComponent {
    private sideDetailService = inject(SideDetailService);
    private dialog = inject(MatDialog);
    private providerApi = inject(ProviderApiService);
    private notificationService = inject(NotificationService);

    providers = input<ProviderListItem[]>([]);
    providerDeleted = output<number>();
    columns: ColumnDef[] = [
        { id: 'id', title: 'ID', width: 'w-20' },
        { id: 'fantasyName', title: 'Nombre' },
        {
            id: 'categories',
            title: 'Rubro',
            accessor: (row: ProviderListItem) =>
                row.categories?.map(c => c.name).join(', ') || '-'
        },
        { id: 'businessName', title: 'Razón Social' },
        { id: 'cuit', title: 'CUIT' },
        { id: 'email', title: 'Email' },
        { id: 'phone', title: 'Teléfono' },
        { id: 'address', title: 'Dirección' },
        { id: 'actions', title: '', width: 'w-24' }
    ]

    onProviderClick(provider: ProviderListItem) {
        this.sideDetailService.open(ProviderDetailComponent, { providerId: Number(provider.id) });
    }

    onEditClick(provider: ProviderListItem) {
        const dialogRef = this.dialog.open(ProviderCreateOrEdit, {
            width: '800px',
            data: { providerId: Number(provider.id) }
        });

        dialogRef.afterClosed().subscribe(result => {
            if (result) {
                console.log('Provider updated:', result);
            }
        });
    }

    onDeleteProvider(provider: ProviderListItem, event: Event) {
        event.stopPropagation();

        const dialogRef = this.dialog.open(ConfirmDialogComponent, {
            data: {
                title: 'Eliminar Proveedor',
                message: `¿Está seguro que desea eliminar el proveedor "${provider.fantasyName}"? Esta acción no se puede deshacer.`,
                confirmText: 'Eliminar',
                cancelText: 'Cancelar'
            },
            disableClose: true,
            width: '450px'
        });

        dialogRef.afterClosed().subscribe(confirmed => {
            if (confirmed) {
                const notificationId = this.notificationService.show({
                    type: 'info',
                    title: 'Eliminando proveedor...',
                    description: 'Por favor espere',
                    loading: true,
                    dismissible: false,
                    duration: 0
                });

                this.providerApi.delete(Number(provider.id)).subscribe({
                    next: () => {
                        this.notificationService.dismiss(notificationId);
                        this.notificationService.success(
                            'Proveedor eliminado',
                            'El proveedor ha sido eliminado exitosamente',
                            3000
                        );
                        this.providerDeleted.emit(Number(provider.id));
                    },
                    error: (error) => {
                        this.notificationService.dismiss(notificationId);
                        this.notificationService.error(
                            'Error al eliminar',
                            error.error?.message || 'No se pudo eliminar el proveedor',
                            5000
                        );
                    }
                });
            }
        });
    }
}
