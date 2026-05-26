import { Component } from '@angular/core';
import { MpIngresoWizardComponent } from '@modules/materias-primas/cobre/components/mp-ingreso-wizard/mp-ingreso-wizard.component';
import { MpCobreHeaderComponent } from '@modules/materias-primas/cobre/components/mp-cobre-header/mp-cobre-header.component';

@Component({
    selector: 'app-mp-cuerda-ingreso-page',
    imports: [MpIngresoWizardComponent, MpCobreHeaderComponent],
    templateUrl: './mp-cuerda-ingreso-page.component.html',
    styleUrl: './mp-cuerda-ingreso-page.component.css',
})
export class MpCuerdaIngresoPageComponent {
    readonly cuerdaTypeId = 5;
}
