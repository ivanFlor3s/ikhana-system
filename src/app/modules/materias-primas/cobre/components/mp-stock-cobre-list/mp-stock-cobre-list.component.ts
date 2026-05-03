import { Component, input, SimpleChanges, inject } from '@angular/core';
import { RawMaterialCobreEntry } from '@interfaces/dtos/response/raw-material-entries.response';
import { ColumnDef, TableComponent, ModernTableCellDirective } from '@shared/components/table/table.component';
import { RawMaterialService } from '@services/raw-material.service';
import { NotificationService } from '@services/notification.service';
import { CommonModule } from '@angular/common';
import { FlaskConical, LucideAngularModule, Send, Tag } from 'lucide-angular';
import { MatTooltipModule } from '@angular/material/tooltip';

@Component({
  selector: 'app-mp-stock-cobre-list',
  imports: [TableComponent, ModernTableCellDirective, CommonModule, LucideAngularModule, MatTooltipModule],
  templateUrl: './mp-stock-cobre-list.component.html',
  styleUrl: './mp-stock-cobre-list.component.css'
})
export class MpStockCobreListComponent {
  private rawMaterialService = inject(RawMaterialService);
  private notificationService = inject(NotificationService);

  data = input.required<RawMaterialCobreEntry[]>();

  readonly flaskConicalIcon = FlaskConical;
  readonly tagIcon = Tag;
  columns: ColumnDef<RawMaterialCobreEntry>[] = [
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
        return row.provider.fantasy_name;
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
    {
      id: 'actions',
      title: '',
      width: 'w-16'
    }
  ]

  onDownloadTestReport(entry: RawMaterialCobreEntry, event: Event) {
    // Stop event propagation to prevent row click
    event.stopPropagation();

    const notificationId = this.notificationService.show({
      type: 'info',
      title: 'Descargando reporte de ensayo...',
      description: 'Por favor espere',
      loading: true,
      dismissible: false,
      duration: 0
    });

    this.rawMaterialService.downloadTestReport(entry.id).subscribe({
      next: (blob) => {
        // Dismiss loading notification
        this.notificationService.dismiss(notificationId);

        // Create a blob URL and trigger download
        const url = window.URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.href = url;
        link.download = `reporte-ensayo-${entry.batch || entry.id}.pdf`;
        link.click();
        window.URL.revokeObjectURL(url);

        // Show success notification
        this.notificationService.success(
          'Reporte de ensayo descargado',
          'El PDF se ha descargado correctamente',
          3000
        );
      },
      error: (error) => {
        // Dismiss loading notification
        this.notificationService.dismiss(notificationId);

        // Show error notification
        this.notificationService.error(
          'Error al descargar',
          error.error?.message || 'No se pudo descargar el reporte de ensayo',
          5000
        );
      }
    });
  }

  onDownloadLabel(entry: RawMaterialCobreEntry, event: Event) {
    // Stop event propagation to prevent row click
    event.stopPropagation();

    const notificationId = this.notificationService.show({
      type: 'info',
      title: 'Descargando etiqueta...',
      description: 'Por favor espere',
      loading: true,
      dismissible: false,
      duration: 0
    });

    this.rawMaterialService.downloadEntryLabel(entry.id).subscribe({
      next: (blob) => {
        // Dismiss loading notification
        this.notificationService.dismiss(notificationId);

        // Create a blob URL and trigger download
        const url = window.URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.href = url;
        link.download = `etiqueta-ingreso-${entry.batch || entry.id}.pdf`;
        link.click();
        window.URL.revokeObjectURL(url);

        // Show success notification
        this.notificationService.success(
          'Etiqueta descargada',
          'El PDF se ha descargado correctamente',
          3000
        );
      },
      error: (error) => {
        // Dismiss loading notification
        this.notificationService.dismiss(notificationId);

        // Show error notification
        this.notificationService.error(
          'Error al descargar',
          error.error?.message || 'No se pudo descargar la etiqueta',
          5000
        );
      }
    });
  }

}
