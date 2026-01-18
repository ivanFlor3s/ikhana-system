import { Component } from '@angular/core';
import { MateriasPrimasHeader } from '../../modules/materias-primas/components/materias-primas-header/materias-primas-header';
import { RouterOutlet } from "@angular/router";

@Component({
  selector: 'app-materias-primas-page',
  imports: [
    MateriasPrimasHeader,
    RouterOutlet
  ],
  templateUrl: './materias-primas-page.component.html',
  styleUrl: './materias-primas-page.component.css'
})
export class MateriasPrimasPageComponent {

  openMateriaPrimaDialog() {
    console.log('Open materia prima dialog');
    // TODO: Implement dialog for creating/editing materias primas
  }
}

