import { Component, inject, OnInit, signal } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ActivatedRoute, Router } from '@angular/router';
import { RawMaterialService } from '@services/raw-material.service';
import { MpCobreSummaryComponent, CobreSummaryData } from '@modules/materias-primas/cobre/components/mp-cobre-summary/mp-cobre-summary.component';
import { NotificationService } from '@services/notification.service';
import { MatProgressSpinnerModule } from '@angular/material/progress-spinner';
import { MatButtonModule } from '@angular/material/button';
import { MatIconModule } from '@angular/material/icon';
import { PageHeaderComponent } from '@shared/components/page-header/page-header.component';
import { ArrowLeft, Printer } from 'lucide-angular';
import { LucideAngularModule } from 'lucide-angular';

@Component({
  selector: 'app-mp-cobre-detalle-page',
  standalone: true,
  imports: [
    CommonModule, 
    MpCobreSummaryComponent, 
    MatProgressSpinnerModule, 
    MatButtonModule, 
    MatIconModule,
    PageHeaderComponent,
    LucideAngularModule
  ],
  templateUrl: './mp-cobre-detalle-page.component.html'
})
export class MpCobreDetallePageComponent implements OnInit {
  private route = inject(ActivatedRoute);
  private router = inject(Router);
  private rawMaterialService = inject(RawMaterialService);
  private notificationService = inject(NotificationService);

  loading = signal(true);
  summaryData = signal<CobreSummaryData | null>(null);
  lote = signal<string>('');

  readonly arrowLeftIcon = ArrowLeft;
  readonly printerIcon = Printer;

  ngOnInit() {
    const idParam = this.route.snapshot.paramMap.get('id');
    if (idParam) {
      this.loadEntry(Number(idParam));
    } else {
      this.router.navigate(['/app/materias-primas/cobre']);
    }
  }

  loadEntry(id: number) {
    this.loading.set(true);
    this.rawMaterialService.getEntryById(id).subscribe({
      next: (response) => {
        const entry = response.data;
        this.lote.set(entry.batch);
        this.summaryData.set({
          fecha: entry.entry_date,
          remito: entry.remito,
          proveedorName: entry.provider?.fantasy_name || entry.provider?.business_name || '',
          pesoKg: entry.quantity_kg,
          diametroMedido: `${entry.characteristic?.decimal_value} ${entry.characteristic?.unit}`,
          resistenciaOhmsKm: entry.test?.resistance_ohm_km,
          estiramientoPercent: entry.test?.elongation_pct || 0,
          fechaEnsayo: entry.test?.test_date,
          aspectoSuperficial: entry.test?.check_winding,
          limpieza: entry.test?.check_cleanliness,
          acondicionado: entry.test?.check_packaging,
          rectificacion: entry.test?.check_identification
        });
        this.loading.set(false);
      },
      error: () => {
        this.notificationService.error('Error', 'No se pudo cargar el detalle del ingreso');
        this.loading.set(false);
        this.goBack();
      }
    });
  }

  goBack() {
    this.router.navigate(['/app/materias-primas/cobre']);
  }

  onPrint() {
    window.print();
  }
}
