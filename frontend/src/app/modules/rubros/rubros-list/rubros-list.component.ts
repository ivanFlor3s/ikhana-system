import { Component, input, inject, output } from '@angular/core';
import { MatButtonModule } from '@angular/material/button';
import { MatDialog } from '@angular/material/dialog';
import { TableComponent, ColumnDef, ModernTableCellDirective } from '@shared/components/table/table.component';
import { CommonModule } from '@angular/common';
import { CategoryApiService, CategoryListResponse } from '../../../generated/category-api.service';
import { SideDetailService } from '../../../services/side-detail.service';
import { ConfirmDialogComponent } from '@shared/components/confirm-dialog/confirm-dialog.component';
import { NotificationService } from '../../../services/notification.service';
import { RubroDetailComponent } from '../components/rubro-detail/rubro-detail.component';
import { take } from 'rxjs/operators';
import { RubroCreateOrEditComponent } from '../dialogs/rubro-create-or-edit/rubro-create-or-edit.component';

@Component({
  selector: 'app-rubros-list',
  imports: [MatButtonModule, TableComponent, ModernTableCellDirective, CommonModule],
  templateUrl: './rubros-list.component.html',
  styleUrl: './rubros-list.component.css'
})
export class RubrosListComponent {
  private sideDetailService = inject(SideDetailService);
  private dialog = inject(MatDialog);
  private categoryApi = inject(CategoryApiService);
  private notificationService = inject(NotificationService);

  rubros = input<CategoryListResponse[]>([]);
  rubroDeleted = output<number>();

  columns: ColumnDef[] = [
    { id: 'id', title: 'ID', width: 'w-20' },
    { id: 'name', title: 'Nombre' },
    { id: 'description', title: 'Descripción' },
    { id: 'actions', title: '', width: 'w-24' }
  ];

  onRubroClick(rubro: CategoryListResponse) {
    this.sideDetailService.open(RubroDetailComponent, { rubroId: Number(rubro.id) });
  }

  onEditRubro(rubro: CategoryListResponse, event: Event) {
    event.stopPropagation();
    this.dialog.open(RubroCreateOrEditComponent, {
      data: { rubro, mode: 'edit' },
      width: '450px',
      disableClose: true
    });
  }

  onDeleteRubro(rubro: CategoryListResponse, event: Event) {
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

    dialogRef.afterClosed().pipe(take(1)).subscribe(confirmed => {
      if (confirmed) {
        const notificationId = this.notificationService.show({
          type: 'info',
          title: 'Eliminando rubro...',
          description: 'Por favor espere',
          loading: true,
          dismissible: false,
          duration: 0
        });

        this.categoryApi.delete(Number(rubro.id)).subscribe({
          next: () => {
            this.notificationService.dismiss(notificationId);
            this.notificationService.success(
              'Rubro eliminado',
              'El rubro ha sido eliminado exitosamente',
              3000
            );
            this.rubroDeleted.emit(Number(rubro.id));
          },
          error: (error) => {
            this.notificationService.dismiss(notificationId);
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

