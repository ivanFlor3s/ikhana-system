import { Component, signal, inject } from '@angular/core';
import { ActivatedRoute } from '@angular/router';
import { MpCobreEditFormComponent } from "@modules/materias-primas/cobre/components/mp-cobre-edit-form/mp-cobre-edit-form.component";
import { MpCobreHeaderComponent } from "@modules/materias-primas/cobre/components/mp-cobre-header/mp-cobre-header.component";

@Component({
  selector: 'app-mp-cobre-editar-page',
  imports: [MpCobreEditFormComponent, MpCobreHeaderComponent],
  templateUrl: './mp-cobre-editar-page.component.html'
})
export class MpCobreEditarPageComponent {
  private route = inject(ActivatedRoute);

  entryId = signal<number | null>(null);

  ngOnInit() {
    const idParam = this.route.snapshot.paramMap.get('id');
    if (idParam) {
      this.entryId.set(Number(idParam));
    }
  }
}
