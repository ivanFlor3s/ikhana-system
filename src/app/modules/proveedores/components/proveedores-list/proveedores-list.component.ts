import { Component, input, inject, output } from '@angular/core';
import { MatTableModule } from '@angular/material/table';
import { MatButtonModule } from '@angular/material/button';
import { MatDialog } from '@angular/material/dialog';
import { TableComponent, ColumnDef, ModernTableCellDirective } from '../../../../shared/components/table/table.component';
import { CommonModule } from '@angular/common';
import { Provider } from '../../../../models/provider.model';
import { SideDetailService } from '../../../../services/side-detail.service';
import { ProviderDetailComponent } from '../provider-detail/provider-detail.component';
import { ProviderService } from '../../../../services/provider.service';
import { ConfirmDialogComponent } from '../../../../shared/components/confirm-dialog/confirm-dialog.component';
import { NotificationService } from '../../../../services/notification.service';
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
    private providerService = inject(ProviderService);
    private notificationService = inject(NotificationService);

    providers = input<Provider[]>([]);
    providerDeleted = output<number>();
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

    onEditClick(provider: Provider) {
        const dialogRef = this.dialog.open(ProviderCreateOrEdit, {
            width: '800px',
            data: { providerId: provider.id }
        });

        dialogRef.afterClosed().subscribe(result => {
            if (result) {
                // Provider was updated, you might want to refresh the list here
                console.log('Provider updated:', result);
            }
        });
    }

    onDeleteProvider(provider: Provider, event: Event) {
        // Stop event propagation to prevent row click
        event.stopPropagation();

        const dialogRef = this.dialog.open(ConfirmDialogComponent, {
            data: {
                title: 'Eliminar Proveedor',
                message: `¿Está seguro que desea eliminar el proveedor "${provider.fantasy_name}"? Esta acción no se puede deshacer.`,
                confirmText: 'Eliminar',
                cancelText: 'Cancelar'
            },
            disableClose: true, // Prevent closing by clicking outside
            width: '450px'
        });

        dialogRef.afterClosed().subscribe(confirmed => {
            if (confirmed) {
                // Show loading snackbar
                const notificationId = this.notificationService.show({
                    type: 'info',
                    title: 'Eliminando proveedor...',
                    description: 'Por favor espere',
                    loading: true,
                    dismissible: false,
                    duration: 0 // Don't auto-dismiss
                });

                // Call delete API
                this.providerService.deleteProvider(provider.id).subscribe({
                    next: (response) => {
                        // Dismiss loading notification
                        this.notificationService.dismiss(notificationId);

                        // Show success notification
                        this.notificationService.success(
                            'Proveedor eliminado',
                            response.message || 'El proveedor ha sido eliminado exitosamente',
                            3000
                        );

                        // Emit event to parent to refresh the list
                        this.providerDeleted.emit(provider.id);
                    },
                    error: (error) => {
                        // Dismiss loading notification
                        this.notificationService.dismiss(notificationId);

                        // Show error notification
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
