import { Component, input, inject, output } from '@angular/core';
import { MatButtonModule } from '@angular/material/button';
import { MatDialog } from '@angular/material/dialog';
import { TableComponent, ColumnDef, ModernTableCellDirective } from '@shared/components/table/table.component';
import { CommonModule } from '@angular/common';
import { Rubro } from '../../../models/rubro.model';
import { SideDetailService } from '../../../services/side-detail.service';
import { RubroService } from '../../../services/rubro.service';
import { ConfirmDialogComponent } from '@shared/components/confirm-dialog/confirm-dialog.component';
import { NotificationService } from '../../../services/notification.service';
import { RubroDetailComponent } from '../components/rubro-detail/rubro-detail.component';

@Component({
  selector: 'app-rubros-list',
  imports: [MatButtonModule, TableComponent, ModernTableCellDirective, CommonModule],
  templateUrl: './rubros-list.component.html',
  styleUrl: './rubros-list.component.css'
})
export class RubrosListComponent {
  private sideDetailService = inject(SideDetailService);
  private dialog = inject(MatDialog);
  private rubroService = inject(RubroService);
  private notificationService = inject(NotificationService);

  rubros = input<Rubro[]>([]);
  rubroDeleted = output<number>();

  columns: ColumnDef[] = [
    {
      id: 'id',
      title: 'ID',
      width: 'w-20'
    },
    {
      id: 'name',
      title: 'Nombre',
    },
    {
      id: 'description',
      title: 'Descripción',
    },
    {
      id: 'actions',
      title: '',
      width: 'w-24'
    }
  ];

  onRubroClick(rubro: Rubro) {
    this.sideDetailService.open(RubroDetailComponent, { rubroId: rubro.id });
  }

  onDeleteRubro(rubro: Rubro, event: Event) {
    event.stopPropagation();

    const dialogRef = this.dialog.open(ConfirmDialogComponent, {
      data: {
        title: 'Eliminar Rubro',
        message: `¿Está seguro que desea eliminar el rubro "${rubro.name}"? Esta acción no se puede deshacer.`,
        confirmText: 'Eliminar',
        cancelText: 'Cancelar'
      },
      disableClose: true,
      width: '450px'
    });

    dialogRef.afterClosed().subscribe(confirmed => {
      if (confirmed) {
        // Show loading snackbar
        const notificationId = this.notificationService.show({
          type: 'info',
          title: 'Eliminando rubro...',
          description: 'Por favor espere',
          loading: true,
          dismissible: false,
          duration: 0
        });

        // Call delete API
        this.rubroService.deleteRubro(rubro.id).subscribe({
          next: (response) => {
            // Dismiss loading notification
            this.notificationService.dismiss(notificationId);

            // Show success notification
            this.notificationService.success(
              'Rubro eliminado',
              response.message || 'El rubro ha sido eliminado exitosamente',
              3000
            );

            // Emit event to parent to refresh the list
            this.rubroDeleted.emit(rubro.id);
          },
          error: (error) => {
            // Dismiss loading notification
            this.notificationService.dismiss(notificationId);

            // Show error notification
            this.notificationService.error(
              'Error al eliminar',
              error.error?.message || 'No se pudo eliminar el rubro',
              5000
            );
          }
        });
      }
    });
  }
}
