import { Component, inject } from '@angular/core';
import { ActivatedRoute } from '@angular/router';
import { MpCobreEditFormComponent } from '@modules/materias-primas/cobre/components/mp-cobre-edit-form/mp-cobre-edit-form.component';

@Component({
    selector: 'app-mp-cuerda-editar-page',
    imports: [MpCobreEditFormComponent],
    templateUrl: './mp-cuerda-editar-page.component.html',
    styleUrl: './mp-cuerda-editar-page.component.css',
})
export class MpCuerdaEditarPageComponent {
    private route = inject(ActivatedRoute);
    entryId = Number(this.route.snapshot.paramMap.get('id'));
    readonly cuerdaTypeId = 5;
}
