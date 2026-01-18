import { Component } from '@angular/core';
import { MpIngresoWizardComponent } from "@modules/materias-primas/cobre/components/mp-ingreso-wizard/mp-ingreso-wizard.component";
import { MpCobreHeaderComponent } from "@modules/materias-primas/cobre/components/mp-cobre-header/mp-cobre-header.component";

@Component({
  selector: 'app-mp-cobre-ingreso-page',
  imports: [MpIngresoWizardComponent, MpCobreHeaderComponent],
  templateUrl: './mp-cobre-ingreso-page.component.html',
  styleUrl: './mp-cobre-ingreso-page.component.css'
})
export class MpCobreIngresoPageComponent {



}
