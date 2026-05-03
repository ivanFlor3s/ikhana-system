import { Component, input } from '@angular/core';
import { CommonModule } from '@angular/common';
import { MatCardModule } from '@angular/material/card';
import { BadgeComponent } from '@shared/components/badge/badge.component';

export interface CobreSummaryData {
    fecha: string;
    remito: string;
    proveedorName: string;
    pesoKg: number;
    diametroMedido: string;
    resistenciaOhmsKm: number;
    estiramientoPercent: number;
    fechaEnsayo: string;
    aspectoSuperficial: boolean;
    limpieza: boolean;
    acondicionado: boolean;
    rectificacion: boolean;
}

@Component({
  selector: 'app-mp-cobre-summary',
  standalone: true,
  imports: [CommonModule, MatCardModule, BadgeComponent],
  templateUrl: './mp-cobre-summary.component.html',
  styleUrl: './mp-cobre-summary.component.css'
})
export class MpCobreSummaryComponent {
  data = input.required<CobreSummaryData>();
}
