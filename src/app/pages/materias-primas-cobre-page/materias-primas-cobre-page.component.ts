import { Component, signal } from '@angular/core';
import { MpCobreHeaderComponent } from "@modules/materias-primas/cobre/components/mp-cobre-header/mp-cobre-header.component";
import { ingresosCobre } from 'app/mocks/ingresos-cobre';
import { MpStockCobreListComponent } from "@modules/materias-primas/cobre/components/mp-stock-cobre-list/mp-stock-cobre-list.component";

@Component({
  selector: 'app-materias-primas-cobre-page',
  imports: [MpCobreHeaderComponent, MpStockCobreListComponent],
  templateUrl: './materias-primas-cobre-page.component.html',
  styleUrl: './materias-primas-cobre-page.component.css'
})
export class MateriasPrimasCobrePageComponent {
  data = signal(ingresosCobre);



}
