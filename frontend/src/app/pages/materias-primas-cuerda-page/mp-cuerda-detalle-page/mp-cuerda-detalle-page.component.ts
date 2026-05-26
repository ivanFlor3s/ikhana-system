import { Component, OnInit, inject, signal } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { CommonModule } from '@angular/common';
import { MpCobreSummaryComponent, CobreSummaryData } from '@modules/materias-primas/cobre/components/mp-cobre-summary/mp-cobre-summary.component';
import { PageHeaderComponent } from '@shared/components/page-header/page-header.component';
import { MatProgressSpinnerModule } from '@angular/material/progress-spinner';
import { MatButtonModule } from '@angular/material/button';
import { CuerdaService } from '@services/cuerda.service';
import { ArrowLeft, Printer } from 'lucide-angular';
import { LucideAngularModule } from 'lucide-angular';

@Component({
    selector: 'app-mp-cuerda-detalle-page',
    imports: [
        CommonModule,
        MpCobreSummaryComponent,
        PageHeaderComponent,
        MatProgressSpinnerModule,
        MatButtonModule,
        LucideAngularModule,
    ],
    templateUrl: './mp-cuerda-detalle-page.component.html',
    styleUrl: './mp-cuerda-detalle-page.component.css',
})
export class MpCuerdaDetallePageComponent implements OnInit {
    private cuerdaService = inject(CuerdaService);
    private route = inject(ActivatedRoute);
    private router = inject(Router);

    readonly arrowLeftIcon = ArrowLeft;
    readonly printerIcon = Printer;

    isLoading = signal(true);
    summaryData = signal<CobreSummaryData | null>(null);

    ngOnInit(): void {
        const id = this.route.snapshot.paramMap.get('id');
        if (id) {
            this.cuerdaService.getEntryById(Number(id)).subscribe({
                next: (response) => {
                    const e = response.data;
                    this.summaryData.set({
                        fecha: e.entry_date,
                        remito: e.remito,
                        proveedorName: e.provider?.fantasy_name ?? '',
                        pesoKg: e.quantity_kg,
                        diametroMedido: e.characteristic?.description ?? '',
                        resistenciaOhmsKm: e.test?.resistance_ohm_km ?? 0,
                        estiramientoPercent: e.test?.elongation_pct ?? 0,
                        fechaEnsayo: e.test?.test_date ?? '',
                        aspectoSuperficial: e.test?.check_winding ?? false,
                        limpieza: e.test?.check_cleanliness ?? false,
                        acondicionado: e.test?.check_packaging ?? false,
                        rectificacion: e.test?.check_identification ?? false,
                    });
                    this.isLoading.set(false);
                },
                error: () => this.isLoading.set(false)
            });
        }
    }

    goBack(): void {
        this.router.navigate(['../..'], { relativeTo: this.route });
    }

    onPrint(): void {
        window.print();
    }
}
